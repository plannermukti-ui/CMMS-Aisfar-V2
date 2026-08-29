<?php

namespace App\Livewire\Plt;

use App\Models\Equipment;
use App\Models\MaterialOrder;
use App\Models\WorkOrder;
use App\Models\WorkOrderSubtaskSparepart;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.user')]
#[Title('Plant - Dashboard')]
class DashboardPage extends Component
{
    public function render()
    {
        $kpis = [
            'total_equipments' => Equipment::count(),
            'active_wo' => WorkOrder::whereNotIn('status', ['closed', 'cancelled'])->count(),
            'breakdown_wo' => WorkOrder::where('job_type', 'breakdown')->whereNotIn('status', ['closed', 'cancelled'])->count(),
            'completed_wo' => WorkOrder::where('status', 'closed')->count(),
            'waiting_spareparts' => WorkOrderSubtaskSparepart::where('status', 'waiting_part')->count(),
            'mol_requests' => MaterialOrder::whereIn('status', ['submitted', 'approved'])->count(),
        ];

        $recentWorkOrders = WorkOrder::with(['equipment.site', 'equipment.reffEquip', 'tasks.subtasks.mechanics'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        $equipmentOverview = Equipment::with(['site', 'reffEquip'])
            ->orderBy('unit')
            ->limit(8)
            ->get();

        return view('livewire.plt.dashboard-page', compact('kpis', 'recentWorkOrders', 'equipmentOverview'));
    }
}
