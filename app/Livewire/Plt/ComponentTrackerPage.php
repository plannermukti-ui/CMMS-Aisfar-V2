<?php

namespace App\Livewire\Plt;

use App\Models\Equipment;
use App\Models\PlantComponent;
use App\Models\PlantComponentMovement;
use App\Traits\SiteFilterable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.user')]
#[Title('Component Tracker & Lifecycle')]
class ComponentTrackerPage extends Component
{
    use SiteFilterable;
    use WithPagination;

    public string $search = '';

    public string $typeFilter = '';

    public string $statusFilter = '';

    // Modal state
    public bool $showFormModal = false;

    public bool $showTransferModal = false;

    public bool $showDetailModal = false;

    public ?string $selectedComponentId = null;

    public ?PlantComponent $activeComponent = null;

    // Component Form Fields
    public string $name = '';

    public string $component_type = 'engine';

    public ?string $serial_number = '';

    public ?string $brand_model = '';

    public ?string $equipment_id = null;

    public ?string $position = '';

    public string $status = 'ready_spare';

    public $accumulated_hours = 0;

    public $target_life_hours = 10000;

    public ?float $installed_at_hm = null;

    public ?string $installed_date = null;

    public ?string $remarks = '';

    // Transfer Modal Fields
    public string $movement_type = 'install';

    public ?string $target_equipment_id = null;

    public ?float $equipment_hm = null;

    public ?string $transfer_position = '';

    public ?string $transfer_notes = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['selectedComponentId', 'name', 'serial_number', 'brand_model', 'equipment_id', 'position', 'installed_at_hm', 'installed_date', 'remarks']);
        $this->component_type = 'engine';
        $this->status = 'ready_spare';
        $this->accumulated_hours = 0;
        $this->target_life_hours = 10000;
        $this->showFormModal = true;
    }

    public function openEditModal(string $id): void
    {
        $comp = PlantComponent::findOrFail($id);
        $this->selectedComponentId = $comp->id;
        $this->name = $comp->name;
        $this->component_type = $comp->component_type;
        $this->serial_number = $comp->serial_number;
        $this->brand_model = $comp->brand_model;
        $this->equipment_id = $comp->equipment_id;
        $this->position = $comp->position;
        $this->status = $comp->status;
        $this->accumulated_hours = (float) $comp->accumulated_hours;
        $this->target_life_hours = (float) $comp->target_life_hours;
        $this->installed_at_hm = $comp->installed_at_hm ? (float) $comp->installed_at_hm : null;
        $this->installed_date = $comp->installed_date ? $comp->installed_date->format('Y-m-d') : null;
        $this->remarks = $comp->remarks;
        $this->showFormModal = true;
    }

    public function saveComponent(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'component_type' => 'required|string',
            'target_life_hours' => 'required|numeric|min:1',
            'accumulated_hours' => 'required|numeric|min:0',
        ]);

        $data = [
            'name' => $this->name,
            'component_type' => $this->component_type,
            'serial_number' => $this->serial_number,
            'brand_model' => $this->brand_model,
            'equipment_id' => $this->equipment_id ?: null,
            'position' => $this->position,
            'status' => $this->equipment_id ? 'installed' : $this->status,
            'accumulated_hours' => $this->accumulated_hours,
            'target_life_hours' => $this->target_life_hours,
            'installed_at_hm' => $this->installed_at_hm,
            'installed_date' => $this->installed_date ?: null,
            'remarks' => $this->remarks,
            'updated_by' => Auth::id(),
        ];

        if ($this->selectedComponentId) {
            $comp = PlantComponent::findOrFail($this->selectedComponentId);
            $comp->update($data);
            session()->flash('success', "Komponen {$comp->component_code} berhasil diperbarui.");
        } else {
            $data['created_by'] = Auth::id();
            $comp = PlantComponent::create($data);
            session()->flash('success', "Komponen baru {$comp->component_code} berhasil ditambahkan.");
        }

        $this->showFormModal = false;
    }

    public function openTransferModal(string $id): void
    {
        $this->activeComponent = PlantComponent::with(['equipment', 'equipment.latestHmLog'])->findOrFail($id);
        $this->selectedComponentId = $this->activeComponent->id;
        $this->movement_type = $this->activeComponent->equipment_id ? 'remove' : 'install';
        $this->target_equipment_id = null;
        $this->equipment_hm = $this->activeComponent->equipment?->current_hm ? (float) $this->activeComponent->equipment->current_hm : null;
        $this->transfer_position = $this->activeComponent->position;
        $this->transfer_notes = '';
        $this->showTransferModal = true;
    }

    public function processTransfer(): void
    {
        $comp = PlantComponent::findOrFail($this->selectedComponentId);
        $fromEquipId = $comp->equipment_id;

        $newStatus = match ($this->movement_type) {
            'install' => 'installed',
            'remove', 'transfer_to_workshop' => 'ready_spare',
            'dispatch_outside' => 'in_outside_repair',
            'receive_outside' => 'ready_spare',
            'scrap' => 'scrapped',
            default => $comp->status,
        };

        $toEquipId = ($this->movement_type === 'install') ? $this->target_equipment_id : null;

        // Record Movement
        PlantComponentMovement::create([
            'component_id' => $comp->id,
            'from_equipment_id' => $fromEquipId,
            'to_equipment_id' => $toEquipId,
            'movement_type' => $this->movement_type,
            'movement_date' => now(),
            'equipment_hm' => $this->equipment_hm,
            'component_hours_at_movement' => $comp->accumulated_hours,
            'performed_by' => Auth::id(),
            'notes' => $this->transfer_notes,
        ]);

        // Update Component State
        $comp->update([
            'equipment_id' => $toEquipId,
            'status' => $newStatus,
            'position' => ($this->movement_type === 'install') ? $this->transfer_position : 'Gudang/Workshop',
            'installed_at_hm' => ($this->movement_type === 'install') ? $this->equipment_hm : null,
            'installed_date' => ($this->movement_type === 'install') ? now()->format('Y-m-d') : null,
            'updated_by' => Auth::id(),
        ]);

        $this->showTransferModal = false;
        session()->flash('success', "Status pergerakan komponen {$comp->component_code} berhasil dicatat.");
    }

    public function openDetailModal(string $id): void
    {
        $this->activeComponent = PlantComponent::with(['equipment', 'movements.fromEquipment', 'movements.toEquipment', 'movements.performer', 'conditionReports', 'outsideRepairs.vendor', 'failureReports'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function render()
    {
        $siteId = self::getCurrentSiteId();

        $query = PlantComponent::with('equipment')
            ->when($siteId, fn ($q) => $q->whereHas('equipment', fn ($eq) => $eq->where('site_id', $siteId)))
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('component_code', 'like', "%{$this->search}%")
                        ->orWhere('serial_number', 'like', "%{$this->search}%")
                        ->orWhere('name', 'like', "%{$this->search}%")
                        ->orWhere('brand_model', 'like', "%{$this->search}%");
                });
            })
            ->when($this->typeFilter, fn ($q) => $q->where('component_type', $this->typeFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter));

        $components = $query->orderBy('created_at', 'desc')->paginate(10);

        $equipments = Equipment::with('reffEquip')
            ->when($siteId, fn ($q) => $q->where('site_id', $siteId))
            ->orderBy('unit')
            ->get();

        // Metrics
        $base = PlantComponent::query()->when($siteId, fn ($q) => $q->whereHas('equipment', fn ($eq) => $eq->where('site_id', $siteId)));
        $totalComponents = (clone $base)->count();
        $installedCount = (clone $base)->where('status', 'installed')->count();
        $readySpareCount = (clone $base)->where('status', 'ready_spare')->count();
        $outsideRepairCount = (clone $base)->where('status', 'in_outside_repair')->count();

        return view('livewire.plt.component-tracker-page', [
            'components' => $components,
            'equipments' => $equipments,
            'totalComponents' => $totalComponents,
            'installedCount' => $installedCount,
            'readySpareCount' => $readySpareCount,
            'outsideRepairCount' => $outsideRepairCount,
        ]);
    }
}
