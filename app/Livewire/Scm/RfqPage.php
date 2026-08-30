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

    // Form fields
    public string $purchase_request_id = '';

    public string $vendor_id = '';

    public string $quotation_number = '';

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

    public function updatedSubtotalDpp(): void
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
        $dppAfterDiscount = max(0, (float) $this->subtotal_dpp - (float) $this->discount_amount);
        $this->ppn_amount = round($dppAfterDiscount * ((float) $this->ppn_percentage / 100), 2);
        $this->grand_total = round($dppAfterDiscount + $this->ppn_amount + (float) $this->shipping_cost, 2);
    }

    public function openCreateModal(?string $prId = null): void
    {
        $this->resetForm();
        if ($prId) {
            $this->purchase_request_id = $prId;
        }
        $this->showFormModal = true;
    }

    public function openCompareModal(string $prId): void
    {
        $this->comparePr = PurchaseRequest::with(['items', 'quotations.vendor'])->findOrFail($prId);
        $this->selectedPrId = $prId;
        $this->showCompareModal = true;
    }

    public function resetForm(): void
    {
        $this->purchase_request_id = $this->selectedPrId ?: '';
        $this->vendor_id = '';
        $this->quotation_number = '';
        $this->subtotal_dpp = 0;
        $this->discount_amount = 0;
        $this->ppn_percentage = 11.00;
        $this->ppn_amount = 0;
        $this->shipping_cost = 0;
        $this->grand_total = 0;
        $this->delivery_lead_time_days = 3;
        $this->notes = '';
    }

    public function saveQuotation(): void
    {
        $this->validate([
            'purchase_request_id' => 'required|exists:purchase_requests,id',
            'vendor_id' => 'required|exists:vendors,id',
            'subtotal_dpp' => 'required|numeric|min:1',
        ]);

        $this->recalculate();

        RfqQuotation::create([
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

        $pr = PurchaseRequest::find($this->purchase_request_id);
        if ($pr && $pr->status === 'approved') {
            $pr->update(['status' => 'rfq_created']);
        }

        $this->showFormModal = false;
        if ($this->showCompareModal && $this->comparePr) {
            $this->comparePr->load(['items', 'quotations.vendor']);
        }
    }

    public function selectWinnerAndGeneratePo(string $quotationId): void
    {
        DB::transaction(function () use ($quotationId) {
            $rfq = RfqQuotation::with('purchaseRequest.items', 'vendor')->findOrFail($quotationId);

            // Mark this quotation as selected, unselect others
            RfqQuotation::where('purchase_request_id', $rfq->purchase_request_id)->update(['is_selected' => false]);
            $rfq->update(['is_selected' => true]);

            // Create Purchase Order
            $po = PurchaseOrder::create([
                'purchase_request_id' => $rfq->purchase_request_id,
                'rfq_quotation_id' => $rfq->id,
                'vendor_id' => $rfq->vendor_id,
                'delivery_target_date' => now()->addDays($rfq->delivery_lead_time_days)->toDateString(),
                'subtotal_dpp' => $rfq->subtotal_dpp,
                'discount_amount' => $rfq->discount_amount,
                'ppn_percentage' => $rfq->ppn_percentage,
                'ppn_amount' => $rfq->ppn_amount,
                'shipping_cost' => $rfq->shipping_cost,
                'grand_total' => $rfq->grand_total,
                'payment_terms' => $rfq->vendor->term_of_payment ?? 'Net 30',
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'notes' => 'Generated from winning quote '.$rfq->rfq_number.' (Vendor: '.$rfq->vendor->name.')',
            ]);

            $rfq->purchaseRequest->update(['status' => 'po_created']);
        });

        $this->showCompareModal = false;
    }

    public function render()
    {
        $approvedPrs = PurchaseRequest::with(['items', 'quotations.vendor'])
            ->whereIn('status', ['approved', 'rfq_created', 'po_created'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $vendors = Vendor::where('is_active', true)->orderBy('name')->get();
        $allPrs = PurchaseRequest::whereIn('status', ['approved', 'rfq_created'])->orderBy('created_at', 'desc')->get();

        return view('livewire.scm.rfq-page', compact('approvedPrs', 'vendors', 'allPrs'));
    }
}
