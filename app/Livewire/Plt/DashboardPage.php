<?php

namespace App\Livewire\Plt;

use App\Models\Equipment;
use App\Models\MaterialOrder;
use App\Models\WorkOrder;
use App\Models\WorkOrderComment;
use App\Models\WorkOrderSubtaskSparepart;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.user')]
#[Title('Plant - Dashboard')]
class DashboardPage extends Component
{
    public function render()
    {
        // ── KPI Cards ─────────────────────────────────────────────
        $kpis = [
            'total_equipments' => Equipment::count(),
            'active_wo' => WorkOrder::whereNotIn('status', ['closed', 'cancelled'])->count(),
            'open_wo' => WorkOrder::where('status', 'open')->count(),
            'in_progress_wo' => WorkOrder::where('status', 'in_progress')->count(),
            'breakdown_wo' => WorkOrder::where('wo_type', 'breakdown')->whereNotIn('status', ['closed', 'cancelled'])->count(),
            'completed_wo' => WorkOrder::whereIn('status', ['completed', 'closed'])->count(),
            'waiting_spareparts' => WorkOrderSubtaskSparepart::where('status', 'waiting_part')->count(),
            'mol_requests' => MaterialOrder::whereIn('status', ['submitted', 'approved'])->count(),
        ];

        // ── Recent Work Orders ────────────────────────────────────
        $recentWorkOrders = WorkOrder::with(['equipment.site', 'equipment.reffEquip', 'tasks.subtasks.mechanics'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // ── Equipment Fleet Overview ──────────────────────────────
        $equipmentOverview = Equipment::with(['site', 'reffEquip'])
            ->orderBy('unit')
            ->limit(8)
            ->get();

        // ── WO by Type (for mini chart) ───────────────────────────
        $woByType = WorkOrder::select('wo_type', DB::raw('count(*) as total'))
            ->groupBy('wo_type')
            ->orderByDesc('total')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->wo_type => $row->total]);

        // ── WO by Status ──────────────────────────────────────────
        $woByStatus = WorkOrder::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->status => $row->total]);

        // ── Recent Discussion Comments ────────────────────────────
        $recentComments = WorkOrderComment::with(['user', 'workOrder'])
            ->whereNull('parent_id')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // ── Downtime top offenders ─────────────────────────────────
        $topDowntime = WorkOrder::with('equipment')
            ->where('downtime_hours', '>', 0)
            ->whereNotNull('downtime_hours')
            ->orderByDesc('downtime_hours')
            ->limit(5)
            ->get();

        return view('livewire.plt.dashboard-page', compact(
            'kpis',
            'recentWorkOrders',
            'equipmentOverview',
            'woByType',
            'woByStatus',
            'recentComments',
            'topDowntime',
        ));
    }
}
