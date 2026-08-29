<?php

namespace App\Livewire\Scm;

use App\Models\Part;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.user')]
#[Title('SCM - Purchase Request (PR)')]
class PrPage extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $filterStatus = 'all';

    public bool $showFormModal = false;

    public bool $showDetailModal = false;

    public ?PurchaseRequest $selectedPr = null;

    // Form fields
    public string $priority = 'medium';

    public ?string $required_date = null;

    public string $remarks = '';

    public array $items = [];

    public function mount(): void
    {
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->priority = 'medium';
        $this->required_date = now()->addDays(7)->format('Y-m-d');
        $this->remarks = '';
        $this->items = [
            [
                'part_id' => '',
                'part_number' => '',
                'part_name' => '',
                'quantity' => 1,
                'uom' => 'Pcs',
                'estimated_unit_price' => 0,
            ],
        ];
    }

    public function updatedItems($value, $key): void
    {
        if (str_ends_with($key, '.part_id') && ! empty($value)) {
            $index = explode('.', $key)[0];
            $part = Part::find($value);
            if ($part) {
                $this->items[$index]['part_number'] = $part->part_number;
                $this->items[$index]['part_name'] = $part->name;
                $this->items[$index]['uom'] = $part->uom;
                $this->items[$index]['estimated_unit_price'] = (float) $part->standard_cost;
            }
        }
    }

    public function addItem(): void
    {
        $this->items[] = [
            'part_id' => '',
            'part_number' => '',
            'part_name' => '',
            'quantity' => 1,
            'uom' => 'Pcs',
            'estimated_unit_price' => 0,
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
        $this->selectedPr = PurchaseRequest::with(['requester', 'approver', 'items.part', 'materialOrder'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function savePr(): void
    {
        $this->validate([
            'items.0.part_name' => 'required|min:2',
            'items.0.quantity' => 'required|numeric|min:0.1',
        ]);

        DB::transaction(function () {
            $pr = PurchaseRequest::create([
                'requester_id' => Auth::id(),
                'priority' => $this->priority,
                'required_date' => $this->required_date ?: null,
                'status' => 'submitted',
                'remarks' => $this->remarks,
            ]);

            foreach ($this->items as $item) {
                if (empty(trim($item['part_name'] ?? ''))) {
                    continue;
                }

                PurchaseRequestItem::create([
                    'purchase_request_id' => $pr->id,
                    'part_id' => ! empty($item['part_id']) ? $item['part_id'] : null,
                    'part_number' => $item['part_number'] ?: '-',
                    'part_name' => $item['part_name'],
                    'quantity' => (float) $item['quantity'],
                    'uom' => $item['uom'] ?? 'Pcs',
                    'estimated_unit_price' => (float) ($item['estimated_unit_price'] ?? 0),
                ]);
            }
        });

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function approvePr(string $id): void
    {
        $pr = PurchaseRequest::findOrFail($id);
        $pr->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        if ($this->showDetailModal && $this->selectedPr) {
            $this->selectedPr->refresh();
        }
    }

    public function render()
    {
        $prs = PurchaseRequest::with(['requester', 'approver', 'items', 'materialOrder'])
            ->when($this->search, function ($q) {
                $term = '%'.strtolower(trim($this->search)).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(pr_number) LIKE ?', [$term])
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

        $parts = Part::where('is_active', true)->orderBy('name')->get();

        return view('livewire.scm.pr-page', compact('prs', 'parts'));
    }
}
