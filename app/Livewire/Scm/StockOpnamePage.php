<?php

namespace App\Livewire\Scm;

use App\Models\Part;
use App\Models\Site;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.user')]
#[Title('SCM - Stock Opname')]
class StockOpnamePage extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterStatus = 'all';

    // Form Modal
    public bool $showFormModal = false;

    public ?string $editId = null;

    public string $opname_date = '';

    public ?string $site_id = '';

    public string $notes = '';

    public string $discrepancy_reason = '';

    public array $opname_items = [];

    // Detail & Berita Acara Modal
    public ?StockOpname $selectedOpname = null;

    public bool $showDetailModal = false;

    public bool $showBeritaAcaraModal = false;

    public function mount(): void
    {
        $this->opname_date = now()->toDateString();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSiteId($value): void
    {
        $this->populateOpnameItems();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->opname_date = now()->toDateString();
        $this->populateOpnameItems();
        $this->showFormModal = true;
    }

    protected function populateOpnameItems(): void
    {
        $allParts = Part::with(['locations.site'])->where('is_active', true)->orderBy('name')->get();
        $items = [];

        foreach ($allParts as $part) {
            $rackLocation = 'Rak A-01';
            $systemStock = (float) $part->stock_on_hand;

            if (! empty($this->site_id)) {
                $siteLocation = $part->locations->where('site_id', $this->site_id)->first();
                if ($siteLocation) {
                    $rackLocation = $siteLocation->rack_location.' ('.$siteLocation->warehouse_name.')';
                    if ((float) $siteLocation->stock_qty > 0) {
                        $systemStock = (float) $siteLocation->stock_qty;
                    }
                } elseif ($part->locations->count() > 0) {
                    $firstLoc = $part->locations->first();
                    $rackLocation = $firstLoc->rack_location;
                }
            } else {
                if ($part->locations && $part->locations->count() > 0) {
                    $rackLocation = $part->locations->map(fn ($l) => ($l->site->site_name ?? 'Gudang').': '.$l->rack_location)->implode(' | ');
                } elseif (! empty($part->bin_location)) {
                    $rackLocation = $part->bin_location;
                }
            }

            $items[] = [
                'part_id' => (string) $part->id,
                'part_number' => $part->part_number,
                'part_name' => $part->name,
                'uom' => $part->uom,
                'rack_location' => $rackLocation,
                'system_stock' => $systemStock,
                'physical_stock' => $systemStock,
                'difference_qty' => 0,
                'unit_cost' => (float) $part->standard_cost,
                'variance_cost' => 0,
                'discrepancy_notes' => '',
            ];
        }

        $this->opname_items = $items;
    }

    public function updatedOpnameItems($value, $key): void
    {
        // recalculate difference_qty & variance_cost when physical_stock or unit_cost is modified
        $parts = explode('.', $key);
        if (count($parts) >= 2) {
            $idx = $parts[0];
            $systemStock = (float) ($this->opname_items[$idx]['system_stock'] ?? 0);
            $physicalStock = (float) ($this->opname_items[$idx]['physical_stock'] ?? 0);
            $unitCost = (float) ($this->opname_items[$idx]['unit_cost'] ?? 0);

            $diff = $physicalStock - $systemStock;
            $this->opname_items[$idx]['difference_qty'] = $diff;
            $this->opname_items[$idx]['variance_cost'] = $diff * $unitCost;
        }
    }

    public function saveOpname(): void
    {
        $this->validate([
            'opname_date' => 'required|date',
            'opname_items' => 'required|array|min:1',
        ], [
            'opname_date.required' => 'Tanggal Stock Opname wajib diisi.',
            'opname_items.min' => 'Minimal 1 item sparepart harus ada dalam Stock Opname.',
        ]);

        DB::transaction(function () {
            $totalSystemItems = count($this->opname_items);
            $totalVarianceQty = 0;
            $totalVarianceVal = 0;

            foreach ($this->opname_items as $item) {
                $diff = (float) ($item['physical_stock'] ?? 0) - (float) ($item['system_stock'] ?? 0);
                $varVal = $diff * (float) ($item['unit_cost'] ?? 0);
                $totalVarianceQty += abs($diff);
                $totalVarianceVal += $varVal;
            }

            $data = [
                'opname_date' => $this->opname_date,
                'site_id' => $this->site_id ?: null,
                'notes' => $this->notes,
                'discrepancy_reason' => $this->discrepancy_reason,
                'total_system_items' => $totalSystemItems,
                'total_variance_qty' => $totalVarianceQty,
                'total_variance_value' => $totalVarianceVal,
            ];

            if ($this->editId) {
                $opname = StockOpname::findOrFail($this->editId);
                $opname->update($data);
                $opname->items()->delete();
            } else {
                $data['conducted_by_id'] = Auth::id();
                $data['status'] = 'submitted';
                $opname = StockOpname::create($data);
            }

            foreach ($this->opname_items as $item) {
                $diff = (float) ($item['physical_stock'] ?? 0) - (float) ($item['system_stock'] ?? 0);
                $unitCost = (float) ($item['unit_cost'] ?? 0);

                StockOpnameItem::create([
                    'stock_opname_id' => $opname->id,
                    'part_id' => $item['part_id'],
                    'part_number' => $item['part_number'],
                    'part_name' => $item['part_name'],
                    'uom' => $item['uom'] ?? 'Pcs',
                    'rack_location' => $item['rack_location'] ?? '-',
                    'system_stock' => (float) ($item['system_stock'] ?? 0),
                    'physical_stock' => (float) ($item['physical_stock'] ?? 0),
                    'difference_qty' => $diff,
                    'unit_cost' => $unitCost,
                    'variance_cost' => $diff * $unitCost,
                    'discrepancy_notes' => $item['discrepancy_notes'] ?? null,
                ]);
            }
        });

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function approveOpname(string $id): void
    {
        DB::transaction(function () use ($id) {
            $opname = StockOpname::with('items')->findOrFail($id);

            // Update physical stock to parts table
            foreach ($opname->items as $item) {
                $part = Part::find($item->part_id);
                if ($part) {
                    $part->update([
                        'stock_on_hand' => $item->physical_stock,
                    ]);
                }
            }

            $opname->update([
                'status' => 'approved',
                'approved_by_id' => Auth::id(),
            ]);
        });

        if ($this->showDetailModal && $this->selectedOpname) {
            $this->selectedOpname->refresh();
        }
    }

    public function rejectOpname(string $id): void
    {
        $opname = StockOpname::findOrFail($id);
        $opname->update([
            'status' => 'rejected',
            'approved_by_id' => Auth::id(),
        ]);

        if ($this->showDetailModal && $this->selectedOpname) {
            $this->selectedOpname->refresh();
        }
    }

    public function openDetail(string $id): void
    {
        $this->selectedOpname = StockOpname::with(['site', 'conductedBy', 'approver', 'items.part'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function openBeritaAcara(string $id): void
    {
        $this->selectedOpname = StockOpname::with(['site', 'conductedBy', 'approver', 'items.part'])->findOrFail($id);
        $this->showBeritaAcaraModal = true;
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->opname_date = now()->toDateString();
        $this->site_id = '';
        $this->notes = '';
        $this->discrepancy_reason = '';
        $this->opname_items = [];
    }

    public function getMetricsProperty(): array
    {
        return [
            'total' => StockOpname::count(),
            'submitted' => StockOpname::where('status', 'submitted')->count(),
            'approved' => StockOpname::where('status', 'approved')->count(),
            'total_variance_value' => StockOpname::where('status', 'approved')->sum('total_variance_value'),
        ];
    }

    public function render()
    {
        $query = StockOpname::with(['site', 'conductedBy', 'approver', 'items'])
            ->when($this->search, function ($q) {
                $term = '%'.strtolower(trim($this->search)).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(opname_number) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(berita_acara_number) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(notes) LIKE ?', [$term])
                        ->orWhereHas('conductedBy', function ($u) use ($term) {
                            $u->whereRaw('LOWER(full_name) LIKE ?', [$term]);
                        });
                });
            })
            ->when($this->filterStatus !== 'all', function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->orderBy('opname_date', 'desc');

        $opnames = $query->paginate(10);
        $sites = Site::orderBy('site_name')->get();

        return view('livewire.scm.stock-opname-page', [
            'opnames' => $opnames,
            'sites' => $sites,
            'metrics' => $this->metrics,
        ]);
    }
}
