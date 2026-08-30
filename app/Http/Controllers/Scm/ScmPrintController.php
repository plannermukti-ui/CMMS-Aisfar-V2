<?php

namespace App\Http\Controllers\Scm;

use App\Http\Controllers\Controller;
use App\Models\DeliveryOrder;
use App\Models\GoodsReceipt;
use App\Models\MaterialOrder;
use App\Models\Part;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\RfqQuotation;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScmPrintController extends Controller
{
    public function printPo(string $id): View
    {
        $po = PurchaseOrder::with(['vendor', 'items.part', 'purchaseRequest', 'approver', 'deliveryOrders.items', 'goodsReceipts.items'])->findOrFail($id);

        return view('print.scm.po', compact('po'));
    }

    public function printDo(string $id): View
    {
        $do = DeliveryOrder::with(['purchaseOrder.vendor', 'destinationSite', 'items.part', 'creator', 'goodsReceipts.items'])->findOrFail($id);

        return view('print.scm.do', compact('do'));
    }

    public function printGr(string $id): View
    {
        $gr = GoodsReceipt::with(['purchaseOrder.vendor', 'deliveryOrder', 'site', 'receiver', 'items.part'])->findOrFail($id);

        return view('print.scm.gr', compact('gr'));
    }

    public function printMol(string $id): View
    {
        $mol = MaterialOrder::with(['requester', 'workOrder.equipment', 'workOrder.site', 'items.part', 'approver'])->findOrFail($id);

        return view('print.scm.mol', compact('mol'));
    }

    public function printPr(string $id): View
    {
        $pr = PurchaseRequest::with(['requester', 'approver', 'items.part', 'materialOrder'])->findOrFail($id);

        return view('print.scm.pr', compact('pr'));
    }

    public function printRfq(string $id): View
    {
        $rfq = RfqQuotation::with(['vendor', 'purchaseRequest.items'])->findOrFail($id);

        return view('print.scm.rfq', compact('rfq'));
    }

    public function printStockOpname(string $id): View
    {
        $opname = StockOpname::with(['site', 'conductedBy', 'approver', 'items.part'])->findOrFail($id);

        return view('print.scm.stock-opname', compact('opname'));
    }

    public function printParts(Request $request): View
    {
        $parts = Part::with('vendor')
            ->when($request->search, function ($q, $search) {
                $term = '%'.strtolower(trim($search)).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(part_number) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(name) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(brand) LIKE ?', [$term]);
                });
            })
            ->when($request->category, fn ($q, $cat) => $q->where('category', $cat))
            ->orderBy('part_number')
            ->get();

        return view('print.scm.parts', compact('parts'));
    }
}
