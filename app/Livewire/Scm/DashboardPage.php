<?php

namespace App\Livewire\Scm;

use App\Models\DeliveryOrder;
use App\Models\MaterialOrder;
use App\Models\Part;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Traits\SiteFilterable;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.user')]
#[Title('SCM - Dashboard')]
class DashboardPage extends Component
{
    use SiteFilterable;

    public function render()
    {
        $siteId = self::getCurrentSiteId();

        $metrics = [
            'mol_pending' => MaterialOrder::whereIn('status', ['submitted', 'draft'])
                ->when($siteId, fn ($q) => $q->whereHas('workOrder.equipment', fn ($eq) => $eq->where('site_id', $siteId)))
                ->count(),
            'pr_pending' => PurchaseRequest::where('status', 'submitted')
                ->when($siteId, fn ($q) => $q->whereHas('materialOrder.workOrder.equipment', fn ($eq) => $eq->where('site_id', $siteId)))
                ->count(),
            'po_active' => PurchaseOrder::whereNotIn('status', ['received', 'cancelled'])
                ->when($siteId, fn ($q) => $q->whereHas('purchaseRequest.materialOrder.workOrder.equipment', fn ($eq) => $eq->where('site_id', $siteId)))
                ->count(),
            'do_in_transit' => DeliveryOrder::where('status', 'in_transit')
                ->when($siteId, fn ($q) => $q->where('destination_site_id', $siteId))
                ->count(),
            'critical_stock' => Part::whereColumn('stock_on_hand', '<=', 'min_stock')
                ->when($siteId, fn ($q) => $q->whereHas('locations', fn ($l) => $l->where('site_id', $siteId)))
                ->count(),
            'total_po_value' => PurchaseOrder::where('status', '!=', 'cancelled')
                ->when($siteId, fn ($q) => $q->whereHas('purchaseRequest.materialOrder.workOrder.equipment', fn ($eq) => $eq->where('site_id', $siteId)))
                ->sum('grand_total'),
        ];

        $recentMols = MaterialOrder::with(['requester', 'workOrder.equipment', 'items'])
            ->when($siteId, fn ($q) => $q->whereHas('workOrder.equipment', fn ($eq) => $eq->where('site_id', $siteId)))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        $recentPos = PurchaseOrder::with(['vendor', 'purchaseRequest'])
            ->when($siteId, fn ($q) => $q->whereHas('purchaseRequest.materialOrder.workOrder.equipment', fn ($eq) => $eq->where('site_id', $siteId)))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        $lowStocks = Part::whereColumn('stock_on_hand', '<=', 'min_stock')
            ->when($siteId, fn ($q) => $q->whereHas('locations', fn ($l) => $l->where('site_id', $siteId)))
            ->orderBy('stock_on_hand', 'asc')
            ->limit(5)
            ->get();

        return view('livewire.scm.dashboard-page', compact('metrics', 'recentMols', 'recentPos', 'lowStocks'));
    }
}
