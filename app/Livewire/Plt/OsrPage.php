<?php

namespace App\Livewire\Plt;

use App\Models\Equipment;
use App\Models\PlantComponent;
use App\Models\PlantOsr;
use App\Models\Vendor;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.user')]
#[Title('OSR - Outside Repair Order')]
class OsrPage extends Component
{
    use WithPagination;

    public string $search = '';

    public string $vendorFilter = '';

    public string $statusFilter = '';

    // Modal state
    public bool $showFormModal = false;

    public bool $showDetailModal = false;

    public bool $showQcModal = false;

    public ?string $selectedOsrId = null;

    public ?PlantOsr $activeOsr = null;

    // Form fields
    public string $order_date = '';

    public ?string $equipment_id = null;

    public ?string $component_id = null;

    public ?string $work_order_id = null;

    public ?string $vendor_id = '';

    public string $item_description = '';

    public string $scope_of_work = '';

    public string $reason_for_outside = 'lack_of_machining_equipment';

    public ?string $dispatch_date = null;

    public ?string $estimated_completion_date = null;

    public ?string $actual_completion_date = null;

    public ?string $delivery_letter_number = '';

    public ?string $received_letter_number = '';

    public $estimated_cost = 0;

    public $actual_cost = 0;

    public int $warranty_period_months = 6;

    public int $warranty_period_hours = 1000;

    public string $status = 'dispatched';

    // QC fields
    public bool $qc_passed = true;

    public string $qc_notes = '';

    protected $paginationTheme = 'bootstrap';

    public function mount(): void
    {
        $this->order_date = now()->format('Y-m-d');
        $this->dispatch_date = now()->format('Y-m-d');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedComponentId($value): void
    {
        if ($value) {
            $comp = PlantComponent::find($value);
            if ($comp) {
                $this->item_description = "Perbaikan Luar: {$comp->name} (S/N: {$comp->serial_number})";
                if ($comp->equipment_id) {
                    $this->equipment_id = $comp->equipment_id;
                }
            }
        }
    }

    public function openCreateModal(): void
    {
        $this->reset([
            'selectedOsrId', 'equipment_id', 'component_id', 'work_order_id', 'vendor_id',
            'item_description', 'scope_of_work', 'delivery_letter_number', 'received_letter_number',
            'actual_completion_date',
        ]);
        $this->order_date = now()->format('Y-m-d');
        $this->dispatch_date = now()->format('Y-m-d');
        $this->estimated_completion_date = now()->addDays(14)->format('Y-m-d');
        $this->reason_for_outside = 'lack_of_machining_equipment';
        $this->estimated_cost = 0;
        $this->actual_cost = 0;
        $this->warranty_period_months = 6;
        $this->warranty_period_hours = 1000;
        $this->status = 'dispatched';
        $this->showFormModal = true;
    }

    public function openEditModal(string $id): void
    {
        $osr = PlantOsr::findOrFail($id);
        $this->selectedOsrId = $osr->id;
        $this->order_date = $osr->order_date->format('Y-m-d');
        $this->equipment_id = $osr->equipment_id;
        $this->component_id = $osr->component_id;
        $this->work_order_id = $osr->work_order_id;
        $this->vendor_id = $osr->vendor_id;
        $this->item_description = $osr->item_description;
        $this->scope_of_work = $osr->scope_of_work ?? '';
        $this->reason_for_outside = $osr->reason_for_outside;
        $this->dispatch_date = $osr->dispatch_date ? $osr->dispatch_date->format('Y-m-d') : null;
        $this->estimated_completion_date = $osr->estimated_completion_date ? $osr->estimated_completion_date->format('Y-m-d') : null;
        $this->actual_completion_date = $osr->actual_completion_date ? $osr->actual_completion_date->format('Y-m-d') : null;
        $this->delivery_letter_number = $osr->delivery_letter_number ?? '';
        $this->received_letter_number = $osr->received_letter_number ?? '';
        $this->estimated_cost = (float) $osr->estimated_cost;
        $this->actual_cost = (float) $osr->actual_cost;
        $this->warranty_period_months = (int) $osr->warranty_period_months;
        $this->warranty_period_hours = (int) $osr->warranty_period_hours;
        $this->status = $osr->status;

        $this->showFormModal = true;
    }

    public function saveOsr(): void
    {
        $this->validate([
            'order_date' => 'required|date',
            'vendor_id' => 'required|exists:vendors,id',
            'item_description' => 'required|string|max:255',
            'scope_of_work' => 'required|string',
        ]);

        $data = [
            'order_date' => $this->order_date,
            'equipment_id' => $this->equipment_id ?: null,
            'component_id' => $this->component_id ?: null,
            'work_order_id' => $this->work_order_id ?: null,
            'vendor_id' => $this->vendor_id,
            'item_description' => $this->item_description,
            'scope_of_work' => $this->scope_of_work,
            'reason_for_outside' => $this->reason_for_outside,
            'dispatch_date' => $this->dispatch_date ?: null,
            'estimated_completion_date' => $this->estimated_completion_date ?: null,
            'actual_completion_date' => $this->actual_completion_date ?: null,
            'delivery_letter_number' => $this->delivery_letter_number,
            'received_letter_number' => $this->received_letter_number,
            'estimated_cost' => $this->estimated_cost,
            'actual_cost' => $this->actual_cost,
            'warranty_period_months' => $this->warranty_period_months,
            'warranty_period_hours' => $this->warranty_period_hours,
            'status' => $this->status,
            'updated_by' => Auth::id(),
        ];

        if ($this->selectedOsrId) {
            $osr = PlantOsr::findOrFail($this->selectedOsrId);
            $osr->update($data);
            session()->flash('success', "Order Perbaikan Luar {$osr->osr_number} berhasil diperbarui.");
        } else {
            $data['created_by'] = Auth::id();
            $osr = PlantOsr::create($data);

            // Update component status if attached
            if ($osr->component_id) {
                PlantComponent::where('id', $osr->component_id)->update(['status' => 'in_outside_repair']);
            }

            session()->flash('success', "Order Perbaikan Luar baru {$osr->osr_number} berhasil dibuat.");
        }

        $this->showFormModal = false;
    }

    public function openDetailModal(string $id): void
    {
        $this->activeOsr = PlantOsr::with(['equipment.reffEquip', 'component', 'vendor', 'workOrder', 'creator'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function openQcModal(string $id): void
    {
        $this->activeOsr = PlantOsr::findOrFail($id);
        $this->selectedOsrId = $this->activeOsr->id;
        $this->qc_passed = true;
        $this->qc_notes = '';
        $this->showQcModal = true;
    }

    public function submitQcReceive(): void
    {
        $osr = PlantOsr::findOrFail($this->selectedOsrId);

        $osr->update([
            'status' => $this->qc_passed ? 'received_at_site' : 'rejected_warranty',
            'qc_passed' => $this->qc_passed,
            'qc_notes' => $this->qc_notes,
            'actual_completion_date' => now()->format('Y-m-d'),
            'received_letter_number' => $this->received_letter_number ?: 'SJ-RCV-'.date('ymd'),
            'updated_by' => Auth::id(),
        ]);

        if ($osr->component_id && $this->qc_passed) {
            PlantComponent::where('id', $osr->component_id)->update(['status' => 'ready_spare']);
        }

        $this->showQcModal = false;
        session()->flash('success', "Penerimaan & QC Inspeksi OSR {$osr->osr_number} berhasil dicatat.");
    }

    public function updateStatus(string $id, string $status): void
    {
        $osr = PlantOsr::findOrFail($id);
        $osr->update(['status' => $status, 'updated_by' => Auth::id()]);
        session()->flash('success', "Status OSR {$osr->osr_number} diubah menjadi: {$osr->status_badge['label']}.");
    }

    public function render()
    {
        $query = PlantOsr::with(['equipment', 'component', 'vendor'])
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('osr_number', 'like', "%{$this->search}%")
                        ->orWhere('item_description', 'like', "%{$this->search}%")
                        ->orWhere('delivery_letter_number', 'like', "%{$this->search}%")
                        ->orWhereHas('vendor', fn ($v) => $v->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->vendorFilter, fn ($q) => $q->where('vendor_id', $this->vendorFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter));

        $orders = $query->orderBy('order_date', 'desc')->paginate(10);

        $equipments = Equipment::with('reffEquip')->orderBy('unit')->get();
        $components = PlantComponent::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();
        $workOrders = WorkOrder::orderBy('wo_number', 'desc')->limit(30)->get();

        // Metrics
        $totalOsr = PlantOsr::count();
        $inProgressCount = PlantOsr::whereIn('status', ['dispatched', 'vendor_inspecting', 'quotation_approved', 'in_progress', 'testing_qc'])->count();
        $receivedCount = PlantOsr::where('status', 'received_at_site')->count();
        $totalCost = PlantOsr::sum('actual_cost');

        return view('livewire.plt.osr-page', [
            'orders' => $orders,
            'equipments' => $equipments,
            'components' => $components,
            'vendors' => $vendors,
            'workOrders' => $workOrders,
            'totalOsr' => $totalOsr,
            'inProgressCount' => $inProgressCount,
            'receivedCount' => $receivedCount,
            'totalCost' => $totalCost,
        ]);
    }
}
