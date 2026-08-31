<?php

namespace App\Livewire\Plt;

use App\Models\Equipment;
use App\Models\PmServiceSchedule;
use App\Services\PreventiveMaintenanceService;
use App\Traits\SiteFilterable;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.user')]
#[Title('PM Monitoring Service - Alat Berat')]
class PmMonitoringPage extends Component
{
    use SiteFilterable;
    use WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    public string $filterMeasurement = '';

    // Modal state
    public bool $showDetailModal = false;

    public bool $showGenerateModal = false;

    public ?PmServiceSchedule $activeSchedule = null;

    public float $generate_hm_km = 0;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openDetailModal(string $id): void
    {
        $this->activeSchedule = PmServiceSchedule::with([
            'equipment.reffEquip',
            'equipment.pmUnitModel',
            'serviceType',
        ])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function openGenerateModal(string $id): void
    {
        $this->activeSchedule = PmServiceSchedule::with([
            'equipment.latestHmLog',
            'serviceType',
        ])->findOrFail($id);
        $this->generate_hm_km = (float) ($this->activeSchedule->equipment->current_hm ?? 0);
        $this->showGenerateModal = true;
    }

    public function confirmGenerate(): void
    {
        $this->validate([
            'generate_hm_km' => 'required|numeric|min:0',
        ]);

        $service = app(PreventiveMaintenanceService::class);
        $wo = $service->generateWorkOrder($this->activeSchedule, (string) $this->generate_hm_km);

        $this->showGenerateModal = false;
        session()->flash('success', "Work Order {$wo->wo_number} berhasil dibuat dari jadwal PM {$this->activeSchedule->serviceType->name}.");
    }

    public function refreshSchedules(): void
    {
        $service = app(PreventiveMaintenanceService::class);
        $siteId = self::getCurrentSiteId();
        $equipments = Equipment::whereNotNull('pm_unit_model_id')
            ->when($siteId, fn ($q) => $q->where('site_id', $siteId))
            ->get();

        foreach ($equipments as $equipment) {
            $service->recalculateSchedules($equipment->id);
        }

        session()->flash('success', 'Semua jadwal PM berhasil di-refresh/rehitung.');
    }

    public function initializeSchedules(): void
    {
        $service = app(PreventiveMaintenanceService::class);
        $count = $service->initializeSchedulesForAll();

        session()->flash('success', "{$count} jadwal PM baru berhasil dibuat/diinisialisasi.");
    }

    public function render()
    {
        $siteId = self::getCurrentSiteId();

        $query = PmServiceSchedule::with([
            'equipment.reffEquip',
            'equipment.pmUnitModel',
            'equipment.latestHmLog',
            'serviceType',
        ])
            ->when($siteId, fn ($q) => $q->whereHas('equipment', fn ($eq) => $eq->where('site_id', $siteId)))
            ->when($this->search, fn ($q) => $q->where(function ($sq) {
                $sq->whereHas('equipment', fn ($eq) => $eq->where('unit', 'like', "%{$this->search}%"))
                    ->orWhereHas('serviceType', fn ($st) => $st->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterMeasurement, fn ($q) => $q->whereHas('serviceType', fn ($st) => $st->where('measurement_type', $this->filterMeasurement)));

        $schedules = $query->orderBy('next_plan_date', 'asc')->paginate(20);

        // Metrics
        $base = PmServiceSchedule::query()->when($siteId, fn ($q) => $q->whereHas('equipment', fn ($eq) => $eq->where('site_id', $siteId)));
        $totalSchedules = (clone $base)->count();
        $overdueCount = (clone $base)->where('status', 'overdue')->count();
        $dueSoonCount = (clone $base)->where('status', 'due_soon')->count();
        $pendingCount = (clone $base)->where('status', 'pending')->count();
        $completedCount = (clone $base)->where('status', 'completed')->count();

        return view('livewire.plt.pm-monitoring-page', compact(
            'schedules',
            'totalSchedules',
            'overdueCount',
            'dueSoonCount',
            'pendingCount',
            'completedCount',
        ));
    }
}
