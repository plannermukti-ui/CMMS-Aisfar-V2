<?php

namespace App\Livewire\Scm;

use App\Models\MaterialOrder;
use App\Models\MaterialOrderItem;
use App\Models\Part;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.user')]
#[Title('SCM - Material Order (MOL)')]
class MolPage extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $filterStatus = 'all';

    // Modal
    public bool $showFormModal = false;

    public bool $showDetailModal = false;

    public ?MaterialOrder $selectedMol = null;

    // Form fields
    public ?string $work_order_id = null;

    public string $notes = '';

    public array $items = [];

    public function mount(): void
    {
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->work_order_id = null;
        $this->notes = '';
        $this->items = [
            [
                'part_id' => '',
                'part_number' => '',
                'part_name' => '',
                'qty_requested' => 1,
                'stock_available' => 0,
            ],
        ];
    }

    public function updatedItems($value, $key): void
    {
        // When part_id is selected, auto-fill part_number, name and check stock
        if (str_ends_with($key, '.part_id') && ! empty($value)) {
            $index = explode('.', $key)[0];
            $part = Part::find($value);
            if ($part) {
                $this->items[$index]['part_number'] = $part->part_number;
                $this->items[$index]['part_name'] = $part->name;
                $this->items[$index]['stock_available'] = (float) $part->stock_on_hand;
            }
        }
    }

    public function addItem(): void
    {
        $this->items[] = [
            'part_id' => '',
            'part_number' => '',
            'part_name' => '',
            'qty_requested' => 1,
            'stock_available' => 0,
        ];
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showFormModal = true;
    }

    public function openDetailModal(string $id): void
    {
        $this->selectedMol = MaterialOrder::with(['requester', 'approver', 'workOrder.equipment', 'items.part'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function saveMol(): void
    {
        $this->validate([
            'items.0.part_name' => 'required|min:2',
            'items.0.qty_requested' => 'required|numeric|min:0.1',
        ], [
            'items.0.part_name.required' => 'Minimal 1 item suku cadang harus diisi.',
        ]);

        DB::transaction(function () {
            $mol = MaterialOrder::create([
                'work_order_id' => $this->work_order_id ?: null,
                'requester_id' => Auth::id(),
                'status' => 'submitted',
                'notes' => $this->notes,
            ]);

            foreach ($this->items as $item) {
                if (empty(trim($item['part_name'] ?? ''))) {
                    continue;
                }

                $part = ! empty($item['part_id']) ? Part::find($item['part_id']) : null;
                $stockOnHand = $part ? (float) $part->stock_on_hand : 0;
                $itemStatus = ($stockOnHand >= (float) $item['qty_requested']) ? 'ready_stock' : 'out_of_stock';

                MaterialOrderItem::create([
                    'material_order_id' => $mol->id,
                    'part_id' => $part ? $part->id : null,
                    'part_number' => $item['part_number'] ?: ($part->part_number ?? '-'),
                    'part_name' => $item['part_name'],
                    'qty_requested' => (float) $item['qty_requested'],
                    'status' => $itemStatus,
                ]);
            }
        });

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function approveAndIssue(string $id): void
    {
        DB::transaction(function () use ($id) {
            $mol = MaterialOrder::with('items.part')->findOrFail($id);

            $totalRequested = 0;
            $totalIssued = 0;

            foreach ($mol->items as $item) {
                $requested = (float) $item->qty_requested;
                $alreadyIssued = (float) $item->qty_issued;
                $needed = max(0, $requested - $alreadyIssued);

                $availableStock = (float) ($item->part?->stock_on_hand ?? 0);
                $qtyToIssue = min($needed, max(0, $availableStock));

                if ($qtyToIssue > 0 && $item->part) {
                    $item->part->decrement('stock_on_hand', $qtyToIssue);
                    $newIssued = $alreadyIssued + $qtyToIssue;

                    $item->update([
                        'qty_issued' => $newIssued,
                        'status' => ($newIssued >= $requested) ? 'issued' : 'partially_issued',
                    ]);
                } elseif ($alreadyIssued <= 0) {
                    $item->update(['status' => 'out_of_stock']);
                }

                $totalRequested += $requested;
                $totalIssued += ((float) $item->fresh()->qty_issued);
            }

            $newStatus = ($totalIssued >= $totalRequested) ? 'issued' : (($totalIssued > 0) ? 'partially_issued' : 'approved');

            $mol->update([
                'status' => $newStatus,
                'approved_by' => Auth::id(),
            ]);
        });

        if ($this->showDetailModal && $this->selectedMol) {
            $this->selectedMol->refresh();
        }

        session()->flash('message', 'MOL berhasil di-Approve. Stok yang tersedia di gudang telah dikeluarkan.');
    }

    public function generateToPr(string $id): void
    {
        DB::transaction(function () use ($id) {
            $mol = MaterialOrder::with('items.part')->findOrFail($id);

            $pr = PurchaseRequest::create([
                'material_order_id' => $mol->id,
                'requester_id' => Auth::id(),
                'priority' => 'high',
                'status' => 'submitted',
                'remarks' => 'Pengadaan sisa suku cadang dari '.$mol->mol_number.($mol->workOrder ? ' (Unit: '.($mol->workOrder->equipment?->unit ?? 'Unit').')' : ''),
            ]);

            $prItemsCreated = 0;
            foreach ($mol->items as $item) {
                $shortageQty = max(0, (float) $item->qty_requested - (float) $item->qty_issued);

                if ($shortageQty > 0 || $item->status === 'out_of_stock') {
                    $actualShortage = ($shortageQty > 0) ? $shortageQty : (float) $item->qty_requested;

                    PurchaseRequestItem::create([
                        'purchase_request_id' => $pr->id,
                        'part_id' => $item->part_id,
                        'part_number' => $item->part_number,
                        'part_name' => $item->part_name,
                        'quantity' => $actualShortage,
                        'uom' => $item->part?->uom ?? 'Pcs',
                        'estimated_unit_price' => $item->part?->standard_cost ?? 0,
                    ]);

                    $item->update(['status' => 'pr_generated']);
                    $prItemsCreated++;
                }
            }

            $isPartiallyIssued = $mol->items->sum('qty_issued') > 0;
            $mol->update([
                'status' => $isPartiallyIssued ? 'partially_issued' : 'converted_to_pr',
            ]);
        });

        if ($this->showDetailModal && $this->selectedMol) {
            $this->selectedMol->refresh();
        }

        session()->flash('message', 'Purchase Request (PR) berhasil dibuat untuk sisa kekurangan suku cadang.');
    }

    public function approveAndGeneratePr(string $id): void
    {
        DB::transaction(function () use ($id) {
            $mol = MaterialOrder::with('items.part')->findOrFail($id);

            $totalRequested = 0;
            $totalIssued = 0;
            $itemsWithShortage = [];

            // 1. Issue whatever is in stock
            foreach ($mol->items as $item) {
                $requested = (float) $item->qty_requested;
                $alreadyIssued = (float) $item->qty_issued;
                $needed = max(0, $requested - $alreadyIssued);

                $availableStock = (float) ($item->part?->stock_on_hand ?? 0);
                $qtyToIssue = min($needed, max(0, $availableStock));

                if ($qtyToIssue > 0 && $item->part) {
                    $item->part->decrement('stock_on_hand', $qtyToIssue);
                    $newIssued = $alreadyIssued + $qtyToIssue;
                    $item->update([
                        'qty_issued' => $newIssued,
                        'status' => ($newIssued >= $requested) ? 'issued' : 'partially_issued',
                    ]);
                }

                $curIssued = (float) $item->fresh()->qty_issued;
                $shortage = max(0, $requested - $curIssued);

                if ($shortage > 0) {
                    $itemsWithShortage[] = [
                        'item' => $item,
                        'shortage' => $shortage,
                    ];
                }

                $totalRequested += $requested;
                $totalIssued += $curIssued;
            }

            // 2. Generate PR for any remaining shortage
            if (count($itemsWithShortage) > 0) {
                $pr = PurchaseRequest::create([
                    'material_order_id' => $mol->id,
                    'requester_id' => Auth::id(),
                    'priority' => 'high',
                    'status' => 'submitted',
                    'remarks' => 'Pengadaan otomatis sisa kekurangan suku cadang dari '.$mol->mol_number.($mol->workOrder ? ' (Unit: '.($mol->workOrder->equipment?->unit ?? 'Unit').')' : ''),
                ]);

                foreach ($itemsWithShortage as $sh) {
                    $item = $sh['item'];
                    $qtyShort = $sh['shortage'];

                    PurchaseRequestItem::create([
                        'purchase_request_id' => $pr->id,
                        'part_id' => $item->part_id,
                        'part_number' => $item->part_number,
                        'part_name' => $item->part_name,
                        'quantity' => $qtyShort,
                        'uom' => $item->part?->uom ?? 'Pcs',
                        'estimated_unit_price' => $item->part?->standard_cost ?? 0,
                    ]);

                    $item->update(['status' => 'pr_generated']);
                }
            }

            $finalStatus = ($totalIssued >= $totalRequested) ? 'issued' : (($totalIssued > 0) ? 'partially_issued' : 'converted_to_pr');

            $mol->update([
                'status' => $finalStatus,
                'approved_by' => Auth::id(),
            ]);
        });

        if ($this->showDetailModal && $this->selectedMol) {
            $this->selectedMol->refresh();
        }

        session()->flash('message', 'MOL berhasil di-Approve (stok keluar) & Purchase Request otomatis dibuat untuk sisa kekurangan.');
    }

    public function render()
    {
        $mols = MaterialOrder::with(['requester', 'approver', 'workOrder.equipment', 'items.part'])
            ->when($this->search, function ($q) {
                $term = '%'.strtolower(trim($this->search)).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(mol_number) LIKE ?', [$term])
                        ->orWhereHas('items', function ($it) use ($term) {
                            $it->whereRaw('LOWER(part_name) LIKE ?', [$term])
                                ->orWhereRaw('LOWER(part_number) LIKE ?', [$term]);
                        });
                });
            })
            ->when($this->filterStatus !== 'all', function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $workOrders = WorkOrder::with('equipment')->whereNotIn('status', ['closed', 'cancelled'])->orderBy('created_at', 'desc')->get();
        $parts = Part::where('is_active', true)->orderBy('name')->get();

        return view('livewire.scm.mol-page', compact('mols', 'workOrders', 'parts'));
    }
}
