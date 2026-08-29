<?php

namespace App\Livewire\Scm;

use App\Models\DeliveryOrder;
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

    public ?DeliveryOrder $selectedDo = null;

    public function openDetailModal(string $id): void
    {
        $this->selectedDo = DeliveryOrder::with(['purchaseOrder.vendor', 'destinationSite', 'items.part', 'creator'])->findOrFail($id);
        $this->showDetailModal = true;
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
        $dos = DeliveryOrder::with(['purchaseOrder.vendor', 'destinationSite', 'items', 'creator'])
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

        return view('livewire.scm.do-page', compact('dos'));
    }
}
