<?php

namespace App\Livewire\Scm;

use App\Models\Part;
use App\Traits\SiteFilterable;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.user')]
#[Title('SCM - Master Spareparts')]
class PartsPage extends Component
{
    use SiteFilterable;
    use WithPagination;

    public string $search = '';

    public string $filterCategory = 'all';

    public string $filterStockStatus = 'all';

    public ?Part $selectedPart = null;

    public bool $showDetailModal = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStockStatus(): void
    {
        $this->resetPage();
    }

    public function openDetail(string $id): void
    {
        $this->selectedPart = Part::with(['vendor', 'locations.site'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function getMetricsProperty(): array
    {
        $siteId = self::getCurrentSiteId();

        $query = Part::query();
        if ($siteId) {
            $query->whereHas('locations', fn ($l) => $l->where('site_id', $siteId));
        }
        $all = $query->get();

        $totalItems = $all->count();
        $totalStockOnHand = $all->sum('stock_on_hand');
        $outOfStock = $all->filter(fn ($p) => (float) $p->stock_on_hand <= 0)->count();
        $lowStock = $all->filter(fn ($p) => (float) $p->stock_on_hand > 0 && (float) $p->stock_on_hand <= (float) $p->min_stock)->count();
        $totalValuation = $all->sum(fn ($p) => ((float) $p->stock_on_hand) * ((float) $p->standard_cost));

        return [
            'total_items' => $totalItems,
            'total_stock_on_hand' => $totalStockOnHand,
            'out_of_stock' => $outOfStock,
            'low_stock' => $lowStock,
            'total_valuation' => $totalValuation,
        ];
    }

    public function render()
    {
        $siteId = self::getCurrentSiteId();

        $query = Part::with(['vendor', 'locations.site'])
            ->when($siteId, fn ($q) => $q->whereHas('locations', fn ($l) => $l->where('site_id', $siteId)))
            ->when($this->search, function ($q) {
                $term = '%'.strtolower(trim($this->search)).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(part_number) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(name) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(category) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(bin_location) LIKE ?', [$term])
                        ->orWhereHas('locations', function ($loc) use ($term) {
                            $loc->whereRaw('LOWER(rack_location) LIKE ?', [$term])
                                ->orWhereRaw('LOWER(warehouse_name) LIKE ?', [$term])
                                ->orWhereHas('site', function ($s) use ($term) {
                                    $s->whereRaw('LOWER(site_name) LIKE ?', [$term]);
                                });
                        });
                });
            })
            ->when($this->filterCategory !== 'all', function ($q) {
                $q->where('category', $this->filterCategory);
            })
            ->when($this->filterStockStatus === 'out_of_stock', function ($q) {
                $q->where('stock_on_hand', '<=', 0);
            })
            ->when($this->filterStockStatus === 'low_stock', function ($q) {
                $q->where('stock_on_hand', '>', 0)
                    ->whereColumn('stock_on_hand', '<=', 'min_stock');
            })
            ->when($this->filterStockStatus === 'ready', function ($q) {
                $q->whereColumn('stock_on_hand', '>', 'min_stock');
            })
            ->orderBy('name');

        $parts = $query->paginate(12);
        $categories = Part::select('category')->distinct()->whereNotNull('category')->pluck('category');

        return view('livewire.scm.parts-page', [
            'parts' => $parts,
            'categories' => $categories,
            'metrics' => $this->metrics,
        ]);
    }
}
