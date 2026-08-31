<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\PmServiceSchedule;
use App\Models\PmServiceType;
use App\Models\PmWorkOrder;
use App\Models\WorkOrder;
use App\Models\WorkOrderSubtask;
use App\Models\WorkOrderTask;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PreventiveMaintenanceService
{
    /**
     * Interval tetap berdasarkan tipe pengukuran.
     */
    private const HM_INTERVALS = [250, 500, 1000, 2000];

    private const KM_INTERVALS = [5000, 10000, 20000, 40000];

    /**
     * Hitung Next Target HM/KM dengan aturan pembulatan tetap (fixed interval).
     *
     * Logika: Jika interval 250 HM dan WO terakhir close di HM 255,
     * maka next target WAJIB di HM 500 (bukan 505).
     *
     * @param  string  $measurementType  'hm' or 'km'
     * @param  int  $intervalValue  250/500/1000/2000 (HM) atau 5000/10000/20000/40000 (KM)
     * @param  float  $currentHmKm  Nilai HM/KM terkini unit
     * @return float Next target HM/KM (pembulatan ke atas ke kelipatan interval)
     */
    public function calculateNextTarget(string $measurementType, int $intervalValue, float $currentHmKm): float
    {
        // Pembulatan ke atas ke kelipatan interval terdekat
        // Contoh: current=255, interval=250 → ceil(255/250)*250 = ceil(1.02)*250 = 2*250 = 500
        $nextTarget = (int) ceil($currentHmKm / $intervalValue) * $intervalValue;

        // Jika hasil pembulatan masih sama dengan current (artinya current tepat pada interval),
        // maka next target adalah current + interval
        if ($nextTarget <= $currentHmKm) {
            $nextTarget += $intervalValue;
        }

        return (float) $nextTarget;
    }

    /**
     * Hitung Next Plan Date berdasarkan selisih HM/KM dibagi target_usage_per_day.
     *
     * @param  float  $currentHmKm  HM/KM terkini unit
     * @param  float  $nextTarget  Next target HM/KM
     * @param  float  $targetUsagePerDay  Target jam/jarak per hari dari master model
     */
    public function calculateNextPlanDate(float $currentHmKm, float $nextTarget, float $targetUsagePerDay): ?Carbon
    {
        if ($targetUsagePerDay <= 0) {
            return null;
        }

        $remainingUsage = $nextTarget - $currentHmKm;

        if ($remainingUsage <= 0) {
            return now(); // Sudah lewat target
        }

        $daysNeeded = (int) ceil($remainingUsage / $targetUsagePerDay);

        return now()->addDays($daysNeeded);
    }

    /**
     * Hitung sisa hari (Remain Day) dari hari ini sampai ke Next Plan Date.
     *
     * @return int Sisa hari (0 jika sudah lewat)
     */
    public function calculateRemainDays(?Carbon $nextPlanDate): int
    {
        if (! $nextPlanDate) {
            return 0;
        }

        $remainDays = (int) now()->diffInDays($nextPlanDate, false);

        return max($remainDays, 0);
    }

    /**
     * Refresh/rehitung semua schedule untuk satu equipment berdasarkan current HM-nya.
     */
    public function recalculateSchedules(string $equipmentId): void
    {
        $equipment = Equipment::with(['pmUnitModel', 'latestHmLog'])->findOrFail($equipmentId);
        $currentHm = (float) ($equipment->current_hm ?? 0);

        // Ambil semua service type yang measurement_type-nya cocok dengan unit model
        if (! $equipment->pm_unit_model_id) {
            return;
        }

        $measurementType = $equipment->pmUnitModel->measurement_type;

        $serviceTypes = PmServiceType::where('measurement_type', $measurementType)
            ->where('status', 'active')
            ->get();

        foreach ($serviceTypes as $serviceType) {
            $schedule = PmServiceSchedule::firstOrCreate(
                [
                    'equipment_id' => $equipmentId,
                    'service_type_id' => $serviceType->id,
                ],
                [
                    'last_executed_hm_km' => 0,
                    'status' => 'pending',
                    'created_by' => Auth::id(),
                ]
            );

            $this->refreshSchedule($schedule, $currentHm);
        }
    }

    /**
     * Refresh satu schedule berdasarkan current HM equipment.
     */
    public function refreshSchedule(PmServiceSchedule $schedule, ?float $currentHm = null): void
    {
        $schedule->loadMissing(['equipment.pmUnitModel', 'equipment.latestHmLog', 'serviceType']);
        $equipment = $schedule->equipment;
        $serviceType = $schedule->serviceType;

        $currentHm = $currentHm ?? (float) ($equipment->current_hm ?? 0);
        $lastExecuted = (float) ($schedule->last_executed_hm_km ?? 0);

        // Hitung next target berdasarkan last executed, bukan current
        $nextTarget = $this->calculateNextTarget(
            $serviceType->measurement_type,
            $serviceType->interval_value,
            $lastExecuted
        );

        // Hitung next plan date
        $unitModel = $equipment->pmUnitModel;
        $targetPerDay = $unitModel ? (float) $unitModel->target_usage_per_day : 8.0;

        $nextPlanDate = $this->calculateNextPlanDate($currentHm, $nextTarget, $targetPerDay);
        $remainDays = $this->calculateRemainDays($nextPlanDate);

        // Tentukan status
        $status = 'pending';
        if ($currentHm >= $nextTarget) {
            $status = 'overdue';
        } elseif ($remainDays <= 7) {
            $status = 'due_soon';
        }

        $schedule->update([
            'next_target_hm_km' => $nextTarget,
            'next_plan_date' => $nextPlanDate,
            'remain_days' => $remainDays,
            'status' => $status,
            'updated_by' => Auth::id(),
        ]);
    }

    /**
     * Generate Work Order dari PM Schedule.
     *
     * Membuat WO baru dan otomatis menyalin Task & Part dari Master Service Type.
     *
     * @return WorkOrder Work Order yang baru dibuat
     */
    public function generateWorkOrder(PmServiceSchedule $schedule, ?string $hmKmAtExecution = null): WorkOrder
    {
        $equipment = $schedule->equipment()->with('site')->first();
        $serviceType = $schedule->serviceType()->with('tasks.parts')->first();

        $currentHm = $hmKmAtExecution ?? ($equipment->current_hm ?? 0);

        return DB::transaction(function () use ($schedule, $equipment, $serviceType, $currentHm) {
            // 1. Buat Work Order baru
            $wo = WorkOrder::create([
                'wo_date' => now()->toDateString(),
                'wo_type' => 'preventive',
                'priority' => 'medium',
                'status' => 'open',
                'equipment_id' => $equipment->id,
                'site_id' => $equipment->site_id,
                'current_hm' => $currentHm,
                'requester_id' => Auth::id(),
                'job_title' => "PM Service: {$serviceType->name} ({$serviceType->interval_label})",
                'problem_description' => "Preventive maintenance {$serviceType->name} untuk unit {$equipment->unit}. "
                    ."Target HM/KM: {$schedule->next_target_hm_km}. "
                    ."Current HM: {$currentHm}.",
                'created_by' => Auth::id(),
            ]);

            // 2. Copy Tasks dari Master Service Type
            $taskOrder = 1;
            foreach ($serviceType->tasks as $pmTask) {
                $woTask = WorkOrderTask::create([
                    'work_order_id' => $wo->id,
                    'problem_title' => $pmTask->task_title,
                    'is_primary' => $taskOrder === 1,
                    'task_order' => $taskOrder,
                    'status' => 'open',
                ]);

                // 3. Copy Parts dari Master Task
                $subtaskOrder = 1;
                foreach ($pmTask->parts as $pmPart) {
                    WorkOrderSubtask::create([
                        'work_order_task_id' => $woTask->id,
                        'action_title' => "{$pmPart->action_type}: {$pmPart->part_name}",
                        'subtask_order' => $subtaskOrder,
                        'status' => 'pending',
                    ]);

                    // Note: Sparepart di WO Subtask (work_order_subtask_spareparts)
                    // bisa ditambahkan jika diperlukan, tapi untuk PM biasanya sparepart
                    // dikelola terpisah di SCM.
                    $subtaskOrder++;
                }

                $taskOrder++;
            }

            // 4. Buat record PM Work Order (junction)
            PmWorkOrder::create([
                'schedule_id' => $schedule->id,
                'work_order_id' => $wo->id,
                'hm_km_at_execution' => $currentHm,
                'execution_date' => now()->toDateString(),
                'status' => 'generated',
                'created_by' => Auth::id(),
            ]);

            // 5. Update schedule: set status ke completed dan update last executed
            $schedule->update([
                'last_executed_hm_km' => $currentHm,
                'last_executed_date' => now()->toDateString(),
                'status' => 'completed',
                'updated_by' => Auth::id(),
            ]);

            return $wo;
        });
    }

    /**
     * Generate schedule awal untuk semua equipment yang punya pm_unit_model.
     * Berguna saat pertama kali setup PM.
     */
    public function initializeSchedulesForAll(): int
    {
        $equipments = Equipment::whereNotNull('pm_unit_model_id')
            ->with(['pmUnitModel', 'latestHmLog'])
            ->get();

        $count = 0;

        foreach ($equipments as $equipment) {
            $measurementType = $equipment->pmUnitModel->measurement_type;
            $serviceTypes = PmServiceType::where('measurement_type', $measurementType)
                ->where('status', 'active')
                ->get();

            foreach ($serviceTypes as $serviceType) {
                $exists = PmServiceSchedule::where('equipment_id', $equipment->id)
                    ->where('service_type_id', $serviceType->id)
                    ->exists();

                if (! $exists) {
                    $currentHm = (float) ($equipment->current_hm ?? 0);
                    $nextTarget = $this->calculateNextTarget(
                        $serviceType->measurement_type,
                        $serviceType->interval_value,
                        $currentHm
                    );

                    $unitModel = $equipment->pmUnitModel;
                    $targetPerDay = (float) $unitModel->target_usage_per_day;
                    $nextPlanDate = $this->calculateNextPlanDate($currentHm, $nextTarget, $targetPerDay);
                    $remainDays = $this->calculateRemainDays($nextPlanDate);

                    $status = 'pending';
                    if ($currentHm >= $nextTarget) {
                        $status = 'overdue';
                    } elseif ($remainDays <= 7) {
                        $status = 'due_soon';
                    }

                    PmServiceSchedule::create([
                        'equipment_id' => $equipment->id,
                        'service_type_id' => $serviceType->id,
                        'last_executed_hm_km' => $currentHm,
                        'next_target_hm_km' => $nextTarget,
                        'next_plan_date' => $nextPlanDate,
                        'remain_days' => $remainDays,
                        'status' => $status,
                        'created_by' => Auth::id(),
                    ]);

                    $count++;
                }
            }
        }

        return $count;
    }
}
