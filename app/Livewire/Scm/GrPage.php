<?php

namespace App\Livewire\Scm;

use App\Models\DeliveryOrder;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Part;
use App\Models\PurchaseOrder;
use App\Models\Site;
use App\Models\WorkOrderSubtaskSparepart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.user')]
#[Title('SCM - Goods Receipt (GR)')]
class GrPage extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public bool $showFormModal = false;

    public bool $showDetailModal = false;

    public ?GoodsReceipt $selectedGr = null;

    // Form fields
    public ?string $delivery_order_id = null;

    public ?string $purchase_order_id = null;

    public ?string $site_id = null;

    public string $delivery_order_number = '';

    public string $notes = '';

    public array $items = [];

    public function mount(): void
    {
        $doId = request()->query('do_id');
        if ($doId) {
            $this->loadFromDo($doId);
        }
    }

    public function loadFromDo(string $doId): void
    {
        $do = DeliveryOrder::with(['items.part', 'purchaseOrder', 'goodsReceipts.items'])->find($doId);
        if ($do) {
            $this->delivery_order_id = $do->id;
            $this->purchase_order_id = $do->purchase_order_id;
            $this->site_id = $do->destination_site_id;
            $this->delivery_order_number = $do->do_number;
            $this->items = [];

            foreach ($do->items as $it) {
                $previouslyReceived = $do->getItemReceivedQuantity($it->part_id, $it->part_number);
                $remaining = max(0, (float) $it->qty_shipped - $previouslyReceived);

                $this->items[] = [
                    'part_id' => $it->part_id,
                    'part_number' => $it->part_number,
                    'part_name' => $it->part_name,
                    'qty_shipped' => (float) $it->qty_shipped,
                    'qty_previously_received' => $previouslyReceived,
                    'qty_remaining' => $remaining,
                    'qty_received' => $remaining,
                    'unit_price' => $it->part->standard_cost ?? 0,
                ];
            }

            $this->showFormModal = true;
        }
    }

    public function updatedDeliveryOrderId($value): void
    {
        if (! empty($value)) {
            $this->loadFromDo($value);
        } else {
            $this->items = [
                [
                    'part_id' => '',
                    'part_number' => '',
                    'part_name' => '',
                    'qty_shipped' => 0,
                    'qty_previously_received' => 0,
                    'qty_remaining' => 1,
                    'qty_received' => 1,
                    'unit_price' => 0,
                ],
            ];
        }
    }

    public function openCreateModal(): void
    {
        $this->delivery_order_id = null;
        $this->purchase_order_id = null;
        $this->site_id = null;
        $this->delivery_order_number = '';
        $this->notes = '';
        $this->items = [
            [
                'part_id' => '',
                'part_number' => '',
                'part_name' => '',
                'qty_shipped' => 0,
                'qty_previously_received' => 0,
                'qty_remaining' => 1,
                'qty_received' => 1,
                'unit_price' => 0,
            ],
        ];
        $this->showFormModal = true;
    }

    public function addItem(): void
    {
        $this->items[] = [
            'part_id' => '',
            'part_number' => '',
            'part_name' => '',
            'qty_shipped' => 0,
            'qty_previously_received' => 0,
            'qty_remaining' => 1,
            'qty_received' => 1,
            'unit_price' => 0,
        ];
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function openDetailModal(string $id): void
    {
        $this->selectedGr = GoodsReceipt::with(['purchaseOrder.vendor', 'deliveryOrder', 'site', 'receiver', 'items.part'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function saveGoodsReceipt(): void
    {
        $this->validate([
            'items.*.qty_received' => 'required|numeric|min:0',
        ]);

        $validItems = array_filter($this->items, fn ($it) => ! empty(trim($it['part_name'] ?? '')) && (float) ($it['qty_received'] ?? 0) > 0);

        if (empty($validItems)) {
            session()->flash('error', 'Pilih minimal 1 item dengan kuantiti diterima lebih dari 0.');

            return;
        }

        $isPartial = false;
        $do = null;
        $po = null;

        if ($this->delivery_order_id) {
            $do = DeliveryOrder::with(['items', 'purchaseOrder', 'goodsReceipts.items'])->find($this->delivery_order_id);
            if ($do) {
                $po = $do->purchaseOrder;
                foreach ($do->items as $doItem) {
                    $prevReceived = $do->getItemReceivedQuantity($doItem->part_id, $doItem->part_number);
                    $currentReceived = 0;
                    foreach ($validItems as $curItem) {
                        if (($curItem['part_id'] && $curItem['part_id'] === $doItem->part_id) || $curItem['part_number'] === $doItem->part_number) {
                            $currentReceived += (float) ($curItem['qty_received'] ?? 0);
                        }
                    }
                    if (($prevReceived + $currentReceived) < (float) $doItem->qty_shipped) {
                        $isPartial = true;
                    }
                }
            }
        } elseif ($this->purchase_order_id) {
            $po = PurchaseOrder::with(['items', 'goodsReceipts.items'])->find($this->purchase_order_id);
            if ($po) {
                foreach ($po->items as $poItem) {
                    $prevReceived = $po->getItemReceivedQuantity($poItem->part_id, $poItem->part_number);
                    $currentReceived = 0;
                    foreach ($validItems as $curItem) {
                        if (($curItem['part_id'] && $curItem['part_id'] === $poItem->part_id) || $curItem['part_number'] === $poItem->part_number) {
                            $currentReceived += (float) ($curItem['qty_received'] ?? 0);
                        }
                    }
                    if (($prevReceived + $currentReceived) < (float) $poItem->quantity) {
                        $isPartial = true;
                    }
                }
            }
        }

        $grStatus = $isPartial ? 'partial' : 'completed';

        DB::transaction(function () use ($validItems, $grStatus, $do, $po) {
            $gr = GoodsReceipt::create([
                'purchase_order_id' => $this->purchase_order_id ?: ($do?->purchase_order_id ?? null),
                'delivery_order_id' => $this->delivery_order_id ?: null,
                'site_id' => $this->site_id ?: null,
                'delivery_order_number' => $this->delivery_order_number,
                'received_by_id' => Auth::id(),
                'status' => $grStatus,
                'notes' => $this->notes,
            ]);

            foreach ($validItems as $item) {
                $part = null;
                if (! empty($item['part_id'])) {
                    $part = Part::find($item['part_id']);
                } elseif (! empty($item['part_number'])) {
                    $part = Part::where('part_number', $item['part_number'])->first();
                }

                // Increment warehouse stock
                if ($part) {
                    $part->increment('stock_on_hand', (float) $item['qty_received']);
                }

                GoodsReceiptItem::create([
                    'goods_receipt_id' => $gr->id,
                    'part_id' => $part ? $part->id : null,
                    'part_number' => $item['part_number'] ?: ($part->part_number ?? '-'),
                    'part_name' => $item['part_name'],
                    'qty_received' => (float) $item['qty_received'],
                    'unit_price' => (float) ($item['unit_price'] ?? 0),
                ]);

                // Update waiting part status on Work Order Subtasks
                if ($part || ! empty($item['part_number'])) {
                    $partNum = $item['part_number'] ?: ($part->part_number ?? '');
                    WorkOrderSubtaskSparepart::where('part_number', $partNum)
                        ->where('status', 'waiting_part')
                        ->update(['status' => 'installed']);
                }
            }

            // Update DO status
            if ($do) {
                $do->updateCalculatedStatus();
            }

            // Update PO status
            if ($po) {
                $po->updateCalculatedStatus();
            }
        });

        session()->flash('message', 'Goods Receipt berhasil disimpan dengan status: '.($grStatus === 'completed' ? 'Diterima Lengkap (Completed)' : 'Diterima Sebagian (Partial)'));
        $this->showFormModal = false;
    }

    public function render()
    {
        $grs = GoodsReceipt::with(['purchaseOrder.vendor', 'deliveryOrder', 'site', 'receiver', 'items'])
            ->when($this->search, function ($q) {
                $term = '%'.strtolower(trim($this->search)).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(gr_number) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(delivery_order_number) LIKE ?', [$term])
                        ->orWhereHas('items', function ($it) use ($term) {
                            $it->whereRaw('LOWER(part_name) LIKE ?', [$term]);
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $pendingDos = DeliveryOrder::with(['purchaseOrder', 'items', 'goodsReceipts.items'])
            ->whereIn('status', ['in_transit', 'arrived', 'partially_received'])
            ->get()
            ->filter(fn ($d) => $d->has_unreceived_items);

        $sites = Site::orderBy('site_name')->get();
        $parts = Part::where('is_active', true)->orderBy('name')->get();

        return view('livewire.scm.gr-page', compact('grs', 'pendingDos', 'sites', 'parts'));
    }
}
