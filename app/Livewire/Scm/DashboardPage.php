<?php

namespace App\Livewire\Scm;

use App\Models\DeliveryOrder;
use App\Models\MaterialOrder;
use App\Models\Part;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.user')]
#[Title('SCM - Dashboard')]
class DashboardPage extends Component
{
    public function render()
    {
        $metrics = [
            'mol_pending' => MaterialOrder::whereIn('status', ['submitted', 'draft'])->count(),
            'pr_pending' => PurchaseRequest::where('status', 'submitted')->count(),
            'po_active' => PurchaseOrder::whereNotIn('status', ['received', 'cancelled'])->count(),
            'do_in_transit' => DeliveryOrder::where('status', 'in_transit')->count(),
            'critical_stock' => Part::whereColumn('stock_on_hand', '<=', 'min_stock')->count(),
            'total_po_value' => PurchaseOrder::where('status', '!=', 'cancelled')->sum('grand_total'),
        ];

        $recentMols = MaterialOrder::with(['requester', 'workOrder.equipment', 'items'])->orderBy('created_at', 'desc')->limit(5)->get();
        $recentPos = PurchaseOrder::with(['vendor', 'purchaseRequest'])->orderBy('created_at', 'desc')->limit(5)->get();
        $lowStocks = Part::whereColumn('stock_on_hand', '<=', 'min_stock')->orderBy('stock_on_hand', 'asc')->limit(5)->get();

        return view('livewire.scm.dashboard-page', compact('metrics', 'recentMols', 'recentPos', 'lowStocks'));
    }
}
