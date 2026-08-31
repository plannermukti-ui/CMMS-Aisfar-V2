<?php

namespace App\Livewire\Plt;

use App\Models\Equipment;
use App\Models\PlantComponent;
use App\Models\PlantFar;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.user')]
#[Title('FAR - Failure Analysis Report')]
class FarPage extends Component
{
    use WithPagination;

    public string $search = '';

    public string $typeFilter = '';

    public string $statusFilter = '';

    // Modal state
    public bool $showFormModal = false;

    public bool $showDetailModal = false;

    public ?string $selectedFarId = null;

    public ?PlantFar $activeFar = null;

    // Form fields
    public string $incident_date = '';

    public ?string $equipment_id = '';

    public ?string $component_id = null;

    public ?string $work_order_id = null;

    public ?string $investigator_id = null;

    public ?float $unit_hm_at_failure = null;

    public ?float $component_hm_at_failure = null;

    public string $failure_type = 'premature_failure';

    public string $failure_title = '';

    public string $problem_statement = '';

    public string $failure_symptoms = '';

    // 5-Why Analysis
    public string $why1 = '';

    public string $why2 = '';

    public string $why3 = '';

    public string $why4 = '';

    public string $why5 = '';

    // Fishbone factors
    public string $factor_man = '';

    public string $factor_machine = '';

    public string $factor_material = '';

    public string $factor_method = '';

    public string $factor_environment = '';

    public string $direct_cause = '';

    public string $root_cause_summary = '';

    public string $corrective_actions = '';

    public string $preventive_actions = '';

    public $cost_impact_estimate = 0;

    public $downtime_hours_estimate = 0;

    public string $status = 'under_investigation';

    protected $paginationTheme = 'bootstrap';

    public function mount(): void
    {
        $this->incident_date = now()->format('Y-m-d');
        $this->investigator_id = Auth::id();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedEquipmentId($value): void
    {
        if ($value) {
            $eq = Equipment::with('latestHmLog')->find($value);
            if ($eq) {
                $this->unit_hm_at_failure = $eq->current_hm ? (float) $eq->current_hm : null;
            }
        }
    }

    public function updatedComponentId($value): void
    {
        if ($value) {
            $comp = PlantComponent::find($value);
            if ($comp) {
                $this->component_hm_at_failure = (float) $comp->accumulated_hours;
                if ($comp->equipment_id) {
                    $this->equipment_id = $comp->equipment_id;
                    $this->unit_hm_at_failure = $comp->equipment?->current_hm ? (float) $comp->equipment->current_hm : null;
                }
            }
        }
    }

    public function openCreateModal(): void
    {
        $this->reset([
            'selectedFarId', 'equipment_id', 'component_id', 'work_order_id', 'unit_hm_at_failure', 'component_hm_at_failure',
            'failure_title', 'problem_statement', 'failure_symptoms',
            'why1', 'why2', 'why3', 'why4', 'why5',
            'factor_man', 'factor_machine', 'factor_material', 'factor_method', 'factor_environment',
            'direct_cause', 'root_cause_summary', 'corrective_actions', 'preventive_actions',
        ]);
        $this->incident_date = now()->format('Y-m-d');
        $this->investigator_id = Auth::id();
        $this->failure_type = 'premature_failure';
        $this->cost_impact_estimate = 0;
        $this->downtime_hours_estimate = 0;
        $this->status = 'under_investigation';
        $this->showFormModal = true;
    }

    public function openEditModal(string $id): void
    {
        $far = PlantFar::findOrFail($id);
        $this->selectedFarId = $far->id;
        $this->incident_date = $far->incident_date->format('Y-m-d');
        $this->equipment_id = $far->equipment_id;
        $this->component_id = $far->component_id;
        $this->work_order_id = $far->work_order_id;
        $this->investigator_id = $far->investigator_id;
        $this->unit_hm_at_failure = $far->unit_hm_at_failure ? (float) $far->unit_hm_at_failure : null;
        $this->component_hm_at_failure = $far->component_hm_at_failure ? (float) $far->component_hm_at_failure : null;
        $this->failure_type = $far->failure_type;
        $this->failure_title = $far->failure_title;
        $this->problem_statement = $far->problem_statement ?? '';
        $this->failure_symptoms = $far->failure_symptoms ?? '';

        $fiveWhy = $far->root_cause_5why ?? [];
        $this->why1 = $fiveWhy['why1'] ?? '';
        $this->why2 = $fiveWhy['why2'] ?? '';
        $this->why3 = $fiveWhy['why3'] ?? '';
        $this->why4 = $fiveWhy['why4'] ?? '';
        $this->why5 = $fiveWhy['why5'] ?? '';

        $fishbone = $far->fishbone_factors ?? [];
        $this->factor_man = $fishbone['man'] ?? '';
        $this->factor_machine = $fishbone['machine'] ?? '';
        $this->factor_material = $fishbone['material'] ?? '';
        $this->factor_method = $fishbone['method'] ?? '';
        $this->factor_environment = $fishbone['environment'] ?? '';

        $this->direct_cause = $far->direct_cause ?? '';
        $this->root_cause_summary = $far->root_cause_summary ?? '';
        $this->corrective_actions = $far->corrective_actions ?? '';
        $this->preventive_actions = $far->preventive_actions ?? '';
        $this->cost_impact_estimate = (float) $far->cost_impact_estimate;
        $this->downtime_hours_estimate = (float) $far->downtime_hours_estimate;
        $this->status = $far->status;

        $this->showFormModal = true;
    }

    public function saveFar(): void
    {
        $this->validate([
            'incident_date' => 'required|date',
            'equipment_id' => 'required|exists:equipments,id',
            'failure_title' => 'required|string|max:255',
            'problem_statement' => 'required|string',
            'direct_cause' => 'required|string',
        ]);

        $fiveWhy = [
            'why1' => $this->why1,
            'why2' => $this->why2,
            'why3' => $this->why3,
            'why4' => $this->why4,
            'why5' => $this->why5,
        ];

        $fishbone = [
            'man' => $this->factor_man,
            'machine' => $this->factor_machine,
            'material' => $this->factor_material,
            'method' => $this->factor_method,
            'environment' => $this->factor_environment,
        ];

        $data = [
            'incident_date' => $this->incident_date,
            'equipment_id' => $this->equipment_id,
            'component_id' => $this->component_id ?: null,
            'work_order_id' => $this->work_order_id ?: null,
            'investigator_id' => $this->investigator_id ?: Auth::id(),
            'unit_hm_at_failure' => $this->unit_hm_at_failure,
            'component_hm_at_failure' => $this->component_hm_at_failure,
            'failure_type' => $this->failure_type,
            'failure_title' => $this->failure_title,
            'problem_statement' => $this->problem_statement,
            'failure_symptoms' => $this->failure_symptoms,
            'root_cause_5why' => $fiveWhy,
            'fishbone_factors' => $fishbone,
            'direct_cause' => $this->direct_cause,
            'root_cause_summary' => $this->root_cause_summary,
            'corrective_actions' => $this->corrective_actions,
            'preventive_actions' => $this->preventive_actions,
            'cost_impact_estimate' => $this->cost_impact_estimate,
            'downtime_hours_estimate' => $this->downtime_hours_estimate,
            'status' => $this->status,
            'updated_by' => Auth::id(),
        ];

        if ($this->selectedFarId) {
            $far = PlantFar::findOrFail($this->selectedFarId);
            $far->update($data);
            session()->flash('success', "Laporan Investigasi FAR {$far->far_number} berhasil diperbarui.");
        } else {
            $data['created_by'] = Auth::id();
            $far = PlantFar::create($data);
            session()->flash('success', "Laporan Investigasi Kerusakan FAR {$far->far_number} berhasil dibuat.");
        }

        $this->showFormModal = false;
    }

    public function openDetailModal(string $id): void
    {
        $this->activeFar = PlantFar::with(['equipment.reffEquip', 'component', 'investigator', 'workOrder'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function render()
    {
        $user = auth()->user();
        $siteId = $user?->getSiteFilterId();

        $query = PlantFar::with(['equipment.reffEquip', 'component', 'investigator'])
            ->when($siteId, fn ($q) => $q->where('equipment_id', function ($sub) use ($siteId) {
                $sub->select('id')->from('equipments')->where('site_id', $siteId);
            }))
            ->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->where('far_number', 'like', "%{$this->search}%")
                        ->orWhere('failure_title', 'like', "%{$this->search}%")
                        ->orWhere('problem_statement', 'like', "%{$this->search}%")
                        ->orWhereHas('equipment', fn ($eq) => $eq->where('unit', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->typeFilter, fn ($q) => $q->where('failure_type', $this->typeFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter));

        $reports = $query->orderBy('incident_date', 'desc')->paginate(10);

        $equipments = Equipment::when($siteId, fn ($q) => $q->where('site_id', $siteId))->with('reffEquip')->orderBy('unit')->get();
        $components = PlantComponent::orderBy('name')->get();
        $workOrders = WorkOrder::orderBy('wo_number', 'desc')->limit(30)->get();

        // Metrics
        $totalFar = PlantFar::count();
        $prematureCount = PlantFar::where('failure_type', 'premature_failure')->count();
        $catastrophicCount = PlantFar::where('failure_type', 'catastrophic_breakdown')->count();
        $closedCount = PlantFar::where('status', 'closed')->count();

        return view('livewire.plt.far-page', [
            'reports' => $reports,
            'equipments' => $equipments,
            'components' => $components,
            'workOrders' => $workOrders,
            'totalFar' => $totalFar,
            'prematureCount' => $prematureCount,
            'catastrophicCount' => $catastrophicCount,
            'closedCount' => $closedCount,
        ]);
    }
}
