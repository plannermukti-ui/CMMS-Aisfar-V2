<?php

namespace App\Services;

use App\Models\EquipmentHm;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HmInterpolationService
{
    /**
     * Record HM for an equipment and automatically interpolate missing days.
     *
     * @param  string  $date  YYYY-MM-DD
     * @param  string  $source  (manual, excel, work_order)
     */
    public function recordHm(string $equipmentId, string $date, int $hmValue, string $source, ?string $userId = null): void
    {
        DB::transaction(function () use ($equipmentId, $date, $hmValue, $source, $userId) {
            // 1. Insert or update the actual HM record
            EquipmentHm::updateOrCreate(
                [
                    'equipment_id' => $equipmentId,
                    'date' => $date,
                ],
                [
                    'hm_value' => $hmValue,
                    'is_interpolated' => false, // This is actual data
                    'source' => $source,
                    'updated_by' => $userId ?? auth()->id(),
                ]
            );

            // 2. Find closest previous actual HM
            $prevRecord = EquipmentHm::where('equipment_id', $equipmentId)
                ->where('date', '<', $date)
                ->where('is_interpolated', false)
                ->orderBy('date', 'desc')
                ->first();

            if ($prevRecord) {
                $this->interpolateGaps($equipmentId, $prevRecord->date->format('Y-m-d'), $prevRecord->hm_value, $date, $hmValue, $userId);
            }

            // 3. Find closest next actual HM
            $nextRecord = EquipmentHm::where('equipment_id', $equipmentId)
                ->where('date', '>', $date)
                ->where('is_interpolated', false)
                ->orderBy('date', 'asc')
                ->first();

            if ($nextRecord) {
                $this->interpolateGaps($equipmentId, $date, $hmValue, $nextRecord->date->format('Y-m-d'), $nextRecord->hm_value, $userId);
            }
        });
    }

    /**
     * Interpolate missing days between Date A and Date B.
     */
    private function interpolateGaps(string $equipmentId, string $dateA, int $hmA, string $dateB, int $hmB, ?string $userId): void
    {
        $start = Carbon::parse($dateA);
        $end = Carbon::parse($dateB);
        $daysDiff = $start->diffInDays($end);

        if ($daysDiff <= 1) {
            return; // No gaps to interpolate
        }

        $hmDiff = $hmB - $hmA;
        $dailyIncrement = $hmDiff / $daysDiff;

        $currentDate = $start->copy()->addDay();
        $currentHm = $hmA;

        while ($currentDate->lt($end)) {
            $currentHm += $dailyIncrement;

            EquipmentHm::updateOrCreate(
                [
                    'equipment_id' => $equipmentId,
                    'date' => $currentDate->format('Y-m-d'),
                ],
                [
                    'hm_value' => round($currentHm),
                    'is_interpolated' => true,
                    'source' => 'interpolated',
                    'updated_by' => $userId ?? auth()->id(),
                ]
            );

            $currentDate->addDay();
        }
    }
}
