<?php

namespace App\Livewire\Scm;

use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\RfqQuotation;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.user')]
#[Title('SCM - Request for Quotation (RFQ)')]
class RfqPage extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public ?string $selectedPrId = null;

    // Modal
    public bool $showFormModal = false;

    public bool $showCompareModal = false;

    public ?PurchaseRequest $comparePr = null;

    public array $selected_winners = [];

    // Form fields
    public string $purchase_request_id = '';

    public string $vendor_id = '';

    public string $quotation_number = '';

    public array $quotation_items = [];

    public $subtotal_dpp = 0;

    public $discount_amount = 0;

    public $ppn_percentage = 11.00;

    public $ppn_amount = 0;

    public $shipping_cost = 0;

    public $grand_total = 0;

    public $delivery_lead_time_days = 3;

    public string $notes = '';

    public function mount(): void
    {
        $prId = request()->query('pr_id');
        if ($prId) {
            $this->purchase_request_id = $prId;
            $this->selectedPrId = $prId;
            $this->openCompareModal($prId);
        }
    }

    public function updatedPurchaseRequestId(): void
    {
        $this->initQuotationItems();
    }

    public function initQuotationItems(): void
    {
        $this->quotation_items = [];
        if ($this->purchase_request_id) {
            $pr = PurchaseRequest::with('items')->find($this->purchase_request_id);
            if ($pr) {
                foreach ($pr->items as $item) {
                    $this->quotation_items[$item->id] = [
                        'purchase_request_item_id' => $item->id,
                        'part_name' => $item->part_name,
                        'qty_req' => $item->quantity,
                        'status' => 'Genuine',
                        'qty_ready' => $item->quantity,
                        'unit_price' => $item->estimated_unit_price,
                        'discount_amount' => 0,
                        'subtotal' => round($item->quantity * $item->estimated_unit_price, 2),
                    ];
                }
            }
        }
        $this->recalculate();
    }

    public function updatedQuotationItems(): void
    {
        $this->recalculate();
    }

    public function updatedDiscountAmount(): void
    {
        $this->recalculate();
    }

    public function updatedPpnPercentage(): void
    {
        $this->recalculate();
    }

    public function updatedShippingCost(): void
    {
        $this->recalculate();
    }

    public function recalculate(): void
    {
        $this->subtotal_dpp = 0;
        foreach ($this->quotation_items as $id => $item) {
            $qty = (float) ($item['qty_ready'] ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            $discount = (float) ($item['discount_amount'] ?? 0);
            $sub = max(0, ($qty * $price) - $discount);
            $this->quotation_items[$id]['subtotal'] = round($sub, 2);
            $this->subtotal_dpp += $sub;
        }

        $dppAfterDiscount = max(0, (float) $this->subtotal_dpp - (float) $this->discount_amount);
        $this->ppn_amount = round($dppAfterDiscount * ((float) $this->ppn_percentage / 100), 2);
        $this->grand_total = round($dppAfterDiscount + $this->ppn_amount + (float) $this->shipping_cost, 2);
    }

    public function openCreateModal(?string $prId = null): void
    {
        $this->resetForm();
        if ($prId) {
            $this->purchase_request_id = $prId;
            $this->initQuotationItems();
        }
        $this->showFormModal = true;
    }

    public function openCompareModal(string $prId): void
    {
        $this->comparePr = PurchaseRequest::with(['items', 'quotations.vendor', 'quotations.items'])->findOrFail($prId);
        $this->selectedPrId = $prId;
        $this->selected_winners = [];
        
        // Auto-select currently selected quotation items if any
        foreach ($this->comparePr->quotations as $q) {
            foreach ($q->items as $qItem) {
                if ($qItem->is_selected) {
                    $this->selected_winners[$qItem->purchase_request_item_id] = $qItem->id;
                }
            }
        }
        
        $this->showCompareModal = true;
    }

    public function resetForm(): void
    {
        $this->purchase_request_id = $this->selectedPrId ?: '';
        $this->vendor_id = '';
        $this->quotation_number = '';
        $this->quotation_items = [];
        $this->subtotal_dpp = 0;
        $this->discount_amount = 0;
        $this->ppn_percentage = 11.00;
        $this->ppn_amount = 0;
        $this->shipping_cost = 0;
        $this->grand_total = 0;
        $this->delivery_lead_time_days = 3;
        $this->notes = '';
        
        if ($this->purchase_request_id) {
            $this->initQuotationItems();
        }
    }

    public function saveQuotation(): void
    {
        $this->validate([
            'purchase_request_id' => 'required|exists:purchase_requests,id',
            'vendor_id' => 'required|exists:vendors,id',
            'quotation_items.*.qty_ready' => 'required|numeric|min:0',
            'quotation_items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $this->recalculate();

        DB::transaction(function () {
            $rfq = RfqQuotation::create([
                'purchase_request_id' => $this->purchase_request_id,
                'vendor_id' => $this->vendor_id,
                'quotation_number' => $this->quotation_number,
                'subtotal_dpp' => $this->subtotal_dpp,
                'discount_amount' => $this->discount_amount,
                'ppn_percentage' => $this->ppn_percentage,
                'ppn_amount' => $this->ppn_amount,
                'shipping_cost' => $this->shipping_cost,
                'grand_total' => $this->grand_total,
                'delivery_lead_time_days' => $this->delivery_lead_time_days,
                'notes' => $this->notes,
            ]);

            foreach ($this->quotation_items as $item) {
                if (((float) $item['qty_ready']) > 0) {
                    $rfq->items()->create([
                        'purchase_request_item_id' => $item['purchase_request_item_id'],
                        'status' => $item['status'] ?? null,
                        'qty_ready' => $item['qty_ready'],
                        'unit_price' => $item['unit_price'],
                        'discount_amount' => $item['discount_amount'] ?? 0,
                        'subtotal' => $item['subtotal'],
                    ]);
                }
            }
        });

        $pr = PurchaseRequest::find($this->purchase_request_id);
        if ($pr && $pr->status === 'approved') {
            $pr->update(['status' => 'rfq_created']);
        }

        $this->showFormModal = false;
        if ($this->showCompareModal && $this->comparePr) {
            $this->comparePr->load(['items', 'quotations.vendor']);
        }
    }

    public function selectWinnerAndGeneratePo(): void
    {
        if (empty($this->selected_winners)) {
            $this->addError('selected_winners', 'Silakan pilih setidaknya satu pemenang.');
            return;
        }

        DB::transaction(function () {
            // Unselect all items for this PR
            $quotationIds = $this->comparePr->quotations->pluck('id');
            \App\Models\RfqQuotationItem::whereIn('rfq_quotation_id', $quotationIds)->update(['is_selected' => false]);
            RfqQuotation::whereIn('id', $quotationIds)->update(['is_selected' => false]);

            // Select chosen items
            $winningQuotationItemIds = array_values($this->selected_winners);
            \App\Models\RfqQuotationItem::whereIn('id', $winningQuotationItemIds)->update(['is_selected' => true]);

            // Load the winning items grouped by Quotation (Vendor)
            $winningItems = \App\Models\RfqQuotationItem::with(['rfqQuotation.vendor', 'purchaseRequestItem.part'])
                ->whereIn('id', $winningQuotationItemIds)
                ->get();
            
            $groupedByQuotation = $winningItems->groupBy('rfq_quotation_id');

            foreach ($groupedByQuotation as $rfqId => $items) {
                $rfq = $items->first()->rfqQuotation;
                $rfq->update(['is_selected' => true]); // Mark RFQ as partially selected

                $subtotal = $items->sum('subtotal');
                $discount = $rfq->discount_amount; // Ongkir and Discount applies to the whole PO. If partial, it might be weird, but let's just use the quotation's discount/shipping as is.
                $dppAfterDiscount = max(0, $subtotal - (float) $discount);
                $ppn = round($dppAfterDiscount * ($rfq->ppn_percentage / 100), 2);
                $shipping = $rfq->shipping_cost;
                $grandTotal = $dppAfterDiscount + $ppn + $shipping;

                $po = PurchaseOrder::create([
                    'purchase_request_id' => $rfq->purchase_request_id,
                    'rfq_quotation_id' => $rfq->id,
                    'vendor_id' => $rfq->vendor_id,
                    'delivery_target_date' => now()->addDays($rfq->delivery_lead_time_days)->toDateString(),
                    'subtotal_dpp' => $subtotal,
                    'discount_amount' => $discount,
                    'ppn_percentage' => $rfq->ppn_percentage,
                    'ppn_amount' => $ppn,
                    'shipping_cost' => $shipping,
                    'grand_total' => $grandTotal,
                    'payment_terms' => $rfq->vendor->term_of_payment ?? 'Net 30',
                    'status' => 'approved',
                    'approved_by' => Auth::id(),
                    'notes' => 'Partial/Full win from quote '.$rfq->rfq_number.' (Vendor: '.$rfq->vendor->name.')',
                ]);

                foreach ($items as $item) {
                    $prItem = $item->purchaseRequestItem;
                    $po->items()->create([
                        'purchase_request_item_id' => $prItem->id,
                        'rfq_quotation_item_id' => $item->id,
                        'part_id' => $prItem->part_id,
                        'part_number' => $prItem->part_number,
                        'part_name' => $prItem->part_name,
                        'quantity' => $item->qty_ready,
                        'uom' => $prItem->uom,
                        'unit_price' => $item->unit_price,
                        'discount_amount' => $item->discount_amount,
                        'subtotal' => $item->subtotal,
                    ]);

                    // Update standard cost of part based on last purchase price
                    if ($prItem->part_id) {
                        $part = \App\Models\Part::find($prItem->part_id);
                        if ($part) {
                            $part->update(['standard_cost' => $item->unit_price]);
                        }
                    }
                }
            }

            $this->comparePr->update(['status' => 'po_created']);
        });

        $this->showCompareModal = false;
    }

    public function render()
    {
        if ($this->showCompareModal && $this->comparePr) {
            $this->comparePr->loadMissing(['items', 'quotations.vendor', 'quotations.items']);
        }

        $approvedPrs = PurchaseRequest::with(['items', 'quotations.vendor'])
            ->whereIn('status', ['approved', 'rfq_created', 'po_created'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $vendors = Vendor::where('is_active', true)->orderBy('name')->get();
        $allPrs = PurchaseRequest::with('items')->whereIn('status', ['approved', 'rfq_created'])->orderBy('created_at', 'desc')->get();

        return view('livewire.scm.rfq-page', compact('approvedPrs', 'vendors', 'allPrs'));
    }
}
