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
#[Title('SCM - Delivery Order (DO)')]
class DoPage extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $filterStatus = 'all';

    public bool $showDetailModal = false;

    public bool $showCreateModal = false;

    public ?DeliveryOrder $selectedDo = null;

    public ?PurchaseOrder $selectedPo = null;

    // Delivery Order create form fields
    public ?string $purchase_order_id = null;

    public string $origin_location = 'Vendor Warehouse';

    public ?string $destination_site_id = null;

    public string $destination_location_name = 'Central Workshop Site';

    public string $expedition_name = '';

    public string $vehicle_plate_number = '';

    public string $tracking_number = '';

    public ?string $estimated_arrival_date = null;

    public string $do_notes = '';

    public array $do_items = [];

    public function openDetailModal(string $id): void
    {
        $this->selectedDo = DeliveryOrder::with(['purchaseOrder.vendor', 'destinationSite', 'items.part', 'creator', 'goodsReceipts.items'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function openCreateModal(?string $poId = null): void
    {
        $this->purchase_order_id = $poId;
        $this->origin_location = 'Vendor Warehouse';
        $this->destination_site_id = null;
        $this->destination_location_name = 'Site Workshop';
        $this->expedition_name = '';
        $this->vehicle_plate_number = '';
        $this->tracking_number = '';
        $this->estimated_arrival_date = now()->addDays(3)->format('Y-m-d\TH:i');
        $this->do_notes = '';
        $this->do_items = [];
        $this->selectedPo = null;

        if ($poId) {
            $this->loadFromPo($poId);
        }

        $this->showCreateModal = true;
    }

    public function updatedPurchaseOrderId($value): void
    {
        if (! empty($value)) {
            $this->loadFromPo($value);
        } else {
            $this->selectedPo = null;
            $this->do_items = [];
        }
    }

    public function loadFromPo(string $poId): void
    {
        $this->selectedPo = PurchaseOrder::with(['items', 'vendor', 'deliveryOrders.items'])->find($poId);
        if ($this->selectedPo) {
            $this->purchase_order_id = $this->selectedPo->id;
            $this->origin_location = 'Vendor ('.$this->selectedPo->vendor->name.')';
            $this->destination_location_name = 'Site Workshop';
            $this->do_items = [];

            foreach ($this->selectedPo->items as $it) {
                $shipped = $this->selectedPo->getItemShippedQuantity($it->part_id, $it->part_number);
                $remaining = max(0, (float) $it->quantity - $shipped);
                if ($remaining > 0) {
                    $this->do_items[] = [
                        'part_id' => $it->part_id,
                        'part_number' => $it->part_number,
                        'part_name' => $it->part_name,
                        'uom' => $it->uom ?: 'Pcs',
                        'qty_ordered' => (float) $it->quantity,
                        'qty_previously_shipped' => $shipped,
                        'qty_remaining' => $remaining,
                        'qty_to_ship' => $remaining,
                    ];
                }
            }
        }
    }

    public function saveDeliveryOrder(): void
    {
        if (! $this->selectedPo) {
            session()->flash('error', 'Silakan pilih Purchase Order (PO) terlebih dahulu.');

            return;
        }

        $itemsToShip = array_filter($this->do_items, fn ($it) => (float) ($it['qty_to_ship'] ?? 0) > 0);

        if (empty($itemsToShip)) {
            session()->flash('error', 'Pilih minimal 1 item dengan kuantiti kirim lebih dari 0.');

            return;
        }

        foreach ($itemsToShip as $it) {
            if ((float) $it['qty_to_ship'] > (float) $it['qty_remaining']) {
                session()->flash('error', 'Kuantiti kirim untuk "'.$it['part_name'].'" tidak boleh melebihi sisa pesanan ('.$it['qty_remaining'].' '.$it['uom'].').');

                return;
            }
        }

        DB::transaction(function () use ($itemsToShip) {
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

            foreach ($itemsToShip as $it) {
                DeliveryOrderItem::create([
                    'delivery_order_id' => $do->id,
                    'part_id' => $it['part_id'] ?: null,
                    'part_number' => $it['part_number'],
                    'part_name' => $it['part_name'],
                    'qty_shipped' => (float) $it['qty_to_ship'],
                    'uom' => $it['uom'] ?: 'Pcs',
                ]);
            }

            $this->selectedPo->updateCalculatedStatus();
        });

        session()->flash('message', 'Delivery Order (DO) berhasil diterbitkan.');
        $this->showCreateModal = false;
    }

    public function markAsArrived(string $id): void
    {
        $do = DeliveryOrder::findOrFail($id);
        $do->update([
            'status' => 'arrived',
            'actual_arrival_date' => now(),
        ]);

        if ($this->showDetailModal && $this->selectedDo) {
            $this->selectedDo->refresh();
        }
    }

    public function render()
    {
        $dos = DeliveryOrder::with(['purchaseOrder.vendor', 'destinationSite', 'items', 'creator', 'goodsReceipts.items'])
            ->when($this->search, function ($q) {
                $term = '%'.strtolower(trim($this->search)).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(do_number) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(expedition_name) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(vehicle_plate_number) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(tracking_number) LIKE ?', [$term]);
                });
            })
            ->when($this->filterStatus !== 'all', function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $availablePos = PurchaseOrder::with(['vendor', 'items', 'deliveryOrders.items'])
            ->whereIn('status', ['approved', 'sent_to_vendor', 'partially_shipped', 'partially_received', 'do_created'])
            ->get()
            ->filter(fn ($p) => $p->has_unshipped_items);

        $sites = Site::orderBy('site_name')->get();

        return view('livewire.scm.do-page', compact('dos', 'availablePos', 'sites'));
    }
}
