<?php

namespace App\Livewire\Scm;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\PurchaseOrder;
use App\Models\Site;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.user')]
#[Title('SCM - Purchase Order (PO)')]
class PoPage extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $filterStatus = 'all';

    public bool $showDetailModal = false;

    public bool $showDoModal = false;

    public ?PurchaseOrder $selectedPo = null;

    // Delivery Order form fields
    public string $origin_location = 'Vendor Warehouse';

    public ?string $destination_site_id = null;

    public string $destination_location_name = 'Central Workshop Site';

    public string $expedition_name = '';

    public string $vehicle_plate_number = '';

    public string $tracking_number = '';

    public ?string $estimated_arrival_date = null;

    public string $do_notes = '';

    public function openDetailModal(string $id): void
    {
        $this->selectedPo = PurchaseOrder::with(['vendor', 'items', 'purchaseRequest', 'approver', 'deliveryOrders'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function openGenerateDoModal(string $poId): void
    {
        $this->selectedPo = PurchaseOrder::with(['items', 'purchaseRequest', 'vendor'])->findOrFail($poId);
        $this->origin_location = 'Vendor ('.$this->selectedPo->vendor->name.')';
        $this->destination_location_name = 'Site Workshop';
        $this->estimated_arrival_date = now()->addDays(3)->format('Y-m-d\TH:i');
        $this->showDoModal = true;
    }

    public function generateDeliveryOrder(): void
    {
        if (! $this->selectedPo) {
            return;
        }

        DB::transaction(function () {
            $do = DeliveryOrder::create([
                'purchase_order_id' => $this->selectedPo->id,
                'origin_location' => $this->origin_location,
                'destination_site_id' => $this->destination_site_id ?: null,
                'destination_location_name' => $this->destination_location_name,
                'expedition_name' => $this->expedition_name,
                'vehicle_plate_number' => $this->vehicle_plate_number,
                'tracking_number' => $this->tracking_number,
                'departure_date' => now(),
                'estimated_arrival_date' => $this->estimated_arrival_date ?: null,
                'status' => 'in_transit',
                'notes' => $this->do_notes,
                'created_by' => Auth::id(),
            ]);

            if ($this->selectedPo->items) {
                foreach ($this->selectedPo->items as $it) {
                    DeliveryOrderItem::create([
                        'delivery_order_id' => $do->id,
                        'part_id' => $it->part_id,
                        'part_number' => $it->part_number,
                        'part_name' => $it->part_name,
                        'qty_shipped' => $it->quantity,
                        'uom' => $it->uom,
                    ]);
                }
            }

            $this->selectedPo->update(['status' => 'do_created']);
        });

        $this->showDoModal = false;
        if ($this->showDetailModal && $this->selectedPo) {
            $this->selectedPo->refresh();
        }
    }

    public function approvePo(string $id): void
    {
        $po = PurchaseOrder::findOrFail($id);
        $po->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        if ($this->showDetailModal && $this->selectedPo) {
            $this->selectedPo->refresh();
        }
    }

    public function cancelPo(string $id): void
    {
        $po = PurchaseOrder::findOrFail($id);
        $po->update(['status' => 'cancelled']);

        if ($this->showDetailModal && $this->selectedPo) {
            $this->selectedPo->refresh();
        }

        session()->flash('message', 'Purchase Order (PO) berhasil dibatalkan.');
    }

    public function deletePo(string $id): void
    {
        $po = PurchaseOrder::findOrFail($id);
        
        if (!in_array($po->status, ['submitted', 'draft', 'cancelled'])) {
            session()->flash('error', 'Hanya PO berstatus draft, submitted, atau cancelled yang dapat dihapus secara permanen.');
            return;
        }

        $po->delete();
        $this->showDetailModal = false;
        session()->flash('message', 'Purchase Order berhasil dihapus.');
    }

    public function render()
    {
        $pos = PurchaseOrder::with(['vendor', 'items', 'approver'])
            ->when($this->search, function ($q) {
                $term = '%'.strtolower(trim($this->search)).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(po_number) LIKE ?', [$term])
                        ->orWhereHas('vendor', function ($v) use ($term) {
                            $v->whereRaw('LOWER(name) LIKE ?', [$term]);
                        });
                });
            })
            ->when($this->filterStatus !== 'all', function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $sites = Site::orderBy('site_name')->get();

        return view('livewire.scm.po-page', compact('pos', 'sites'));
    }
}
