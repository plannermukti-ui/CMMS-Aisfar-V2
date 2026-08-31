<?php

namespace App\Livewire\Plt;

use App\Models\PmWorkOrder;
use App\Traits\SiteFilterable;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.user')]
#[Title('PM History Service')]
class PmHistoryPage extends Component
{
    use SiteFilterable;
    use WithPagination;

    public string $search = '';

    public string $filterStatus = '';

    public bool $showDetailModal = false;

    public ?PmWorkOrder $activePmWo = null;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openDetailModal(string $id): void
    {
        $this->activePmWo = PmWorkOrder::with([
            'workOrder.equipment.reffEquip',
            'workOrder.site',
            'workOrder.requester',
            'workOrder.tasks.subtasks',
            'schedule.serviceType',
            'schedule.equipment',
        ])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function render()
    {
        $siteId = self::getCurrentSiteId();

        $query = PmWorkOrder::with([
            'workOrder.equipment.reffEquip',
            'workOrder.site',
            'workOrder.requester',
            'schedule.serviceType',
            'schedule.equipment',
        ])
            ->when($siteId, fn ($q) => $q->whereHas('workOrder.equipment', fn ($eq) => $eq->where('site_id', $siteId)))
            ->when($this->search, fn ($q) => $q->where(function ($sq) {
                $sq->whereHas('workOrder', fn ($woq) => $woq->where('wo_number', 'like', "%{$this->search}%"))
                    ->orWhereHas('schedule.equipment', fn ($eq) => $eq->where('unit', 'like', "%{$this->search}%"))
                    ->orWhereHas('schedule.serviceType', fn ($st) => $st->where('name', 'like', "%{$this->search}%"));
            }))
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus));

        $pmWorkOrders = $query->orderByDesc('execution_date')->paginate(20);

        // Metrics
        $base = PmWorkOrder::query()->when($siteId, fn ($q) => $q->whereHas('workOrder.equipment', fn ($eq) => $eq->where('site_id', $siteId)));
        $totalHistory = (clone $base)->count();
        $completedCount = (clone $base)->where('status', 'completed')->count();
        $inProgressCount = (clone $base)->where('status', 'in_progress')->count();
        $generatedCount = (clone $base)->where('status', 'generated')->count();

        return view('livewire.plt.pm-history-page', compact(
            'pmWorkOrders',
            'totalHistory',
            'completedCount',
            'inProgressCount',
            'generatedCount',
        ));
    }
}
