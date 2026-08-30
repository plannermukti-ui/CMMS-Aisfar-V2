<?php

namespace App\Livewire\Plt;

use App\Models\Equipment;
use App\Models\PlantCcr;
use App\Models\PlantComponent;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.user')]
#[Title('CCR - Component Condition Report')]
class CcrPage extends Component
{
    use WithPagination;

    public string $search = '';

    public string $recommendationFilter = '';

    public string $statusFilter = '';

    // Modal state
    public bool $showFormModal = false;

    public bool $showDetailModal = false;

    public ?string $selectedCcrId = null;

    public ?PlantCcr $activeCcr = null;

    // Form fields
    public string $ccr_date = '';

    public ?string $equipment_id = '';

    public ?string $component_id = null;

    public string $component_name = '';

    public ?float $current_unit_hm = null;

    public ?float $component_running_hours = null;

    public $wear_percentage = 0;

    public string $physical_condition = 'fair_wear';

    public string $leakage_status = 'none';

    public string $noise_vibration_status = 'normal';

    public string $oil_contamination_status = 'clean';

    public string $findings_description = '';

    public string $recommendation = 'continue_run';

    public ?float $estimated_remaining_hours = null;

    public ?string $inspector_id = null;

    public string $status = 'submitted';

    protected $paginationTheme = 'bootstrap';

    public function mount(): void
    {
        $this->ccr_date = now()->format('Y-m-d');
        $this->inspector_id = Auth::id();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedEquipmentId($value): void
    {
        if ($value) {
            $eq = Equipment::find($value);
            if ($eq) {
                $this->current_unit_hm = $eq->current_hm ? (float) $eq->current_hm : null;
            }
        }
    }

    public function updatedComponentId($value): void
    {
        if ($value) {
            $comp = PlantComponent::find($value);
            if ($comp) {
                $this->component_name = $comp->name;
                $this->component_running_hours = (float) $comp->accumulated_hours;
                if ($comp->equipment_id) {
                    $this->equipment_id = $comp->equipment_id;
                    $this->current_unit_hm = $comp->equipment?->current_hm ? (float) $comp->equipment->current_hm : null;
                }
            }
        }
    }

    public function openCreateModal(): void
    {
        $this->reset(['selectedCcrId', 'equipment_id', 'component_id', 'component_name', 'current_unit_hm', 'component_running_hours', 'findings_description', 'estimated_remaining_hours']);
        $this->ccr_date = now()->format('Y-m-d');
        $this->inspector_id = Auth::id();
        $this->wear_percentage = 0;
        $this->physical_condition = 'fair_wear';
        $this->leakage_status = 'none';
        $this->noise_vibration_status = 'normal';
        $this->oil_contamination_status = 'clean';
        $this->recommendation = 'continue_run';
        $this->status = 'submitted';
        $this->showFormModal = true;
    }

    public function openEditModal(string $id): void
    {
        $ccr = PlantCcr::findOrFail($id);
        $this->selectedCcrId = $ccr->id;
        $this->ccr_date = $ccr->ccr_date->format('Y-m-d');
        $this->equipment_id = $ccr->equipment_id;
        $this->component_id = $ccr->component_id;
        $this->component_name = $ccr->component_name;
        $this->current_unit_hm = $ccr->current_unit_hm ? (float) $ccr->current_unit_hm : null;
        $this->component_running_hours = $ccr->component_running_hours ? (float) $ccr->component_running_hours : null;
        $this->wear_percentage = (float) $ccr->wear_percentage;
        $this->physical_condition = $ccr->physical_condition;
        $this->leakage_status = $ccr->leakage_status;
        $this->noise_vibration_status = $ccr->noise_vibration_status;
        $this->oil_contamination_status = $ccr->oil_contamination_status;
        $this->findings_description = $ccr->findings_description ?? '';
        $this->recommendation = $ccr->recommendation;
        $this->estimated_remaining_hours = $ccr->estimated_remaining_hours ? (float) $ccr->estimated_remaining_hours : null;
        $this->inspector_id = $ccr->inspector_id;
        $this->status = $ccr->status;
        $this->showFormModal = true;
    }

    public function saveCcr(): void
    {
        $this->validate([
            'equipment_id' => 'required|exists:equipments,id',
            'component_name' => 'required|string|max:255',
            'ccr_date' => 'required|date',
            'wear_percentage' => 'required|numeric|min:0|max:100',
            'findings_description' => 'required|string',
        ]);

        $data = [
            'ccr_date' => $this->ccr_date,
            'equipment_id' => $this->equipment_id,
            'component_id' => $this->component_id ?: null,
            'component_name' => $this->component_name,
            'current_unit_hm' => $this->current_unit_hm,
            'component_running_hours' => $this->component_running_hours,
            'wear_percentage' => $this->wear_percentage,
            'physical_condition' => $this->physical_condition,
            'leakage_status' => $this->leakage_status,
            'noise_vibration_status' => $this->noise_vibration_status,
            'oil_contamination_status' => $this->oil_contamination_status,
            'findings_description' => $this->findings_description,
            'recommendation' => $this->recommendation,
            'estimated_remaining_hours' => $this->estimated_remaining_hours,
            'inspector_id' => $this->inspector_id ?: Auth::id(),
            'status' => $this->status,
            'updated_by' => Auth::id(),
        ];

        if ($this->selectedCcrId) {
            $ccr = PlantCcr::findOrFail($this->selectedCcrId);
            $ccr->update($data);
            session()->flash('success', "Laporan CCR {$ccr->ccr_number} berhasil diperbarui.");
        } else {
            $data['created_by'] = Auth::id();
            $ccr = PlantCcr::create($data);
            session()->flash('success', "Laporan CCR baru {$ccr->ccr_number} berhasil dibuat.");
        }

        $this->showFormModal = false;
    }

    public function openDetailModal(string $id): void
    {
        $this->activeCcr = PlantCcr::with(['equipment.reffEquip', 'component', 'inspector', 'workOrder'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function createWorkOrderFromCcr(string $id): void
    {
        $ccr = PlantCcr::findOrFail($id);

        if ($ccr->work_order_id) {
            session()->flash('success', "Work Order sudah pernah dibuat sebelumnya untuk CCR ini ({$ccr->workOrder?->wo_number}).");

            return;
        }

        $wo = WorkOrder::create([
            'wo_date' => now()->format('Y-m-d'),
            'wo_type' => in_array($ccr->recommendation, ['immediate_replace', 'rebuild_overhaul']) ? 'corrective' : 'preventive',
            'priority' => ($ccr->recommendation === 'immediate_replace') ? 'emergency' : 'high',
            'status' => 'open',
            'equipment_id' => $ccr->equipment_id,
            'current_hm' => $ccr->current_unit_hm,
            'requester_id' => Auth::id(),
            'job_title' => "Follow-up CCR: {$ccr->component_name} ({$ccr->ccr_number})",
            'problem_description' => "Rekomendasi CCR {$ccr->recommendation_badge['label']}: {$ccr->findings_description}",
            'created_by' => Auth::id(),
        ]);

        $ccr->update([
            'work_order_id' => $wo->id,
            'action_taken' => 'work_order_created',
        ]);

        session()->flash('success', "Berhasil membuat Work Order {$wo->wo_number} dari laporan CCR {$ccr->ccr_number}.");
    }

    public function render()
    {
        $query = PlantCcr::with(['equipment.reffEquip', 'component', 'inspector', 'workOrder'])
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('ccr_number', 'like', "%{$this->search}%")
                        ->orWhere('component_name', 'like', "%{$this->search}%")
                        ->orWhereHas('equipment', fn ($eq) => $eq->where('unit', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->recommendationFilter, fn ($q) => $q->where('recommendation', $this->recommendationFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter));

        $reports = $query->orderBy('ccr_date', 'desc')->paginate(10);

        $equipments = Equipment::with('reffEquip')->orderBy('unit')->get();
        $components = PlantComponent::orderBy('name')->get();

        // Metrics
        $totalCcr = PlantCcr::count();
        $urgentReplaceCount = PlantCcr::where('recommendation', 'immediate_replace')->count();
        $scheduleChangeCount = PlantCcr::where('recommendation', 'schedule_changeout')->count();
        $rebuildCount = PlantCcr::where('recommendation', 'rebuild_overhaul')->count();

        return view('livewire.plt.ccr-page', [
            'reports' => $reports,
            'equipments' => $equipments,
            'components' => $components,
            'totalCcr' => $totalCcr,
            'urgentReplaceCount' => $urgentReplaceCount,
            'scheduleChangeCount' => $scheduleChangeCount,
            'rebuildCount' => $rebuildCount,
        ]);
    }
}
