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
        // ── Site Filter ──────────────────────────────────────────
        $user = auth()->user();
        $siteId = $user?->getSiteFilterId();
        $siteName = $user?->site?->site_name ?? 'All Sites';

        // ── KPI Cards ─────────────────────────────────────────────
        $kpis = [
            'total_equipments' => Equipment::when($siteId, fn ($q) => $q->where('site_id', $siteId))->count(),
            'active_wo' => WorkOrder::when($siteId, fn ($q) => $q->where('site_id', $siteId))->whereNotIn('status', ['closed', 'cancelled'])->count(),
            'open_wo' => WorkOrder::when($siteId, fn ($q) => $q->where('site_id', $siteId))->where('status', 'open')->count(),
            'in_progress_wo' => WorkOrder::when($siteId, fn ($q) => $q->where('site_id', $siteId))->where('status', 'in_progress')->count(),
            'breakdown_wo' => WorkOrder::when($siteId, fn ($q) => $q->where('site_id', $siteId))->where('wo_type', 'breakdown')->whereNotIn('status', ['closed', 'cancelled'])->count(),
            'completed_wo' => WorkOrder::when($siteId, fn ($q) => $q->where('site_id', $siteId))->whereIn('status', ['completed', 'closed'])->count(),
            'waiting_spareparts' => WorkOrderSubtaskSparepart::where('status', 'waiting_part')->count(),
            'mol_requests' => MaterialOrder::whereIn('status', ['submitted', 'approved'])->count(),
        ];

        // ── Recent Work Orders ────────────────────────────────────
        $recentWorkOrders = WorkOrder::when($siteId, fn ($q) => $q->where('site_id', $siteId))
            ->with(['equipment.site', 'equipment.reffEquip', 'tasks.subtasks.mechanics'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // ── Equipment Fleet Overview ──────────────────────────────
        $equipmentOverview = Equipment::when($siteId, fn ($q) => $q->where('site_id', $siteId))
            ->with(['site', 'reffEquip'])
            ->orderBy('unit')
            ->limit(8)
            ->get();

        // ── WO by Type (for mini chart) ───────────────────────────
        $woByType = WorkOrder::when($siteId, fn ($q) => $q->where('site_id', $siteId))
            ->select('wo_type', DB::raw('count(*) as total'))
            ->groupBy('wo_type')
            ->orderByDesc('total')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->wo_type => $row->total]);

        // ── WO by Status ──────────────────────────────────────────
        $woByStatus = WorkOrder::when($siteId, fn ($q) => $q->where('site_id', $siteId))
            ->select('status', DB::raw('count(*) as total'))
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
        $topDowntime = WorkOrder::when($siteId, fn ($q) => $q->where('site_id', $siteId))
            ->with('equipment')
            ->where('downtime_hours', '>', 0)
            ->whereNotNull('downtime_hours')
            ->orderByDesc('downtime_hours')
            ->limit(5)
            ->get();

        return view('livewire.plt.dashboard-page', compact(
            'kpis', 'recentWorkOrders', 'equipmentOverview', 'woByType', 'woByStatus', 'recentComments', 'topDowntime', 'siteName',
        ));
    }
}
