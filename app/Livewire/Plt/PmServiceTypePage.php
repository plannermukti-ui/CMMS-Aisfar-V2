<?php

namespace App\Livewire\Plt;

use App\Models\PmServiceType;
use App\Models\PmServiceTypePart;
use App\Models\PmServiceTypeTask;
use App\Models\PmUnitModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.user')]
#[Title('PM Service Types - Master Data')]
class PmServiceTypePage extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterMeasurement = '';

    // Modal states
    public bool $showServiceTypeModal = false;

    public bool $showTaskModal = false;

    public bool $showPartModal = false;

    public bool $showDetailModal = false;

    // Service Type form
    public ?string $selectedServiceTypeId = null;

    public string $st_name = '';

    public ?string $st_pm_unit_model_id = null;

    public string $st_measurement_type = 'hm';

    public int $st_interval_value = 250;

    public string $st_description = '';

    public string $st_status = 'active';

    // Task form
    public ?string $selectedTaskId = null;

    public ?string $task_service_type_id = null;

    public string $task_title = '';

    public int $task_order = 1;

    public string $task_notes = '';

    // Part form
    public ?string $selectedPartId = null;

    public ?string $part_task_id = null;

    public string $part_number = '';

    public string $part_name = '';

    public float $part_quantity = 1;

    public string $part_unit = 'Pcs';

    public string $part_action_type = 'replace';

    public string $part_remarks = '';

    // Detail
    public ?PmServiceType $activeServiceType = null;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // ── Service Type CRUD ─────────────────────────────────────

    public function openServiceTypeModal(?string $id = null): void
    {
        if ($id) {
            $st = PmServiceType::findOrFail($id);
            $this->selectedServiceTypeId = $st->id;
            $this->st_name = $st->name;
            $this->st_pm_unit_model_id = $st->pm_unit_model_id;
            $this->st_measurement_type = $st->measurement_type;
            $this->st_interval_value = $st->interval_value;
            $this->st_description = $st->description ?? '';
            $this->st_status = $st->status;
        } else {
            $this->reset(['selectedServiceTypeId', 'st_name', 'st_description', 'st_pm_unit_model_id']);
            $this->st_measurement_type = 'hm';
            $this->st_interval_value = 250;
            $this->st_status = 'active';
        }

        $this->showServiceTypeModal = true;
    }

    /**
     * When PM Unit Model is selected, auto-fill measurement type from the model.
     */
    public function updatedStPmUnitModelId(?string $value): void
    {
        if ($value) {
            $model = PmUnitModel::find($value);
            if ($model) {
                $this->st_measurement_type = $model->measurement_type;
            }
        }
    }

    public function saveServiceType(): void
    {
        $this->validate([
            'st_name' => 'required|string|max:255|unique:pm_service_types,name,'.($this->selectedServiceTypeId ?? ''),
            'st_measurement_type' => 'required|in:hm,km',
            'st_interval_value' => 'required|integer|min:1',
            'st_status' => 'required|in:active,inactive',
        ]);

        $data = [
            'name' => $this->st_name,
            'pm_unit_model_id' => $this->st_pm_unit_model_id ?: null,
            'measurement_type' => $this->st_measurement_type,
            'interval_value' => $this->st_interval_value,
            'description' => $this->st_description,
            'status' => $this->st_status,
            'updated_by' => Auth::id(),
        ];

        if ($this->selectedServiceTypeId) {
            PmServiceType::findOrFail($this->selectedServiceTypeId)->update($data);
            session()->flash('success', 'Service Type berhasil diperbarui.');
        } else {
            $data['created_by'] = Auth::id();
            PmServiceType::create($data);
            session()->flash('success', 'Service Type baru berhasil dibuat.');
        }

        $this->showServiceTypeModal = false;
    }

    // ── Task CRUD ─────────────────────────────────────────────

    public function openTaskModal(?string $serviceTypeId = null, ?string $taskId = null): void
    {
        if ($taskId) {
            $task = PmServiceTypeTask::findOrFail($taskId);
            $this->selectedTaskId = $task->id;
            $this->task_service_type_id = $task->service_type_id;
            $this->task_title = $task->task_title;
            $this->task_order = $task->task_order;
            $this->task_notes = $task->notes ?? '';
        } else {
            $this->reset(['selectedTaskId', 'task_title', 'task_notes']);
            $this->task_service_type_id = $serviceTypeId;
            $this->task_order = PmServiceTypeTask::where('service_type_id', $serviceTypeId)->max('task_order') + 1;
        }

        $this->showTaskModal = true;
    }

    public function saveTask(): void
    {
        $this->validate([
            'task_service_type_id' => 'required|exists:pm_service_types,id',
            'task_title' => 'required|string|max:255',
            'task_order' => 'required|integer|min:1',
        ]);

        $data = [
            'service_type_id' => $this->task_service_type_id,
            'task_title' => $this->task_title,
            'task_order' => $this->task_order,
            'notes' => $this->task_notes,
            'updated_by' => Auth::id(),
        ];

        if ($this->selectedTaskId) {
            PmServiceTypeTask::findOrFail($this->selectedTaskId)->update($data);
            session()->flash('success', 'Task berhasil diperbarui.');
        } else {
            $data['created_by'] = Auth::id();
            PmServiceTypeTask::create($data);
            session()->flash('success', 'Task baru berhasil ditambahkan.');
        }

        $this->showTaskModal = false;
    }

    // ── Part CRUD ─────────────────────────────────────────────

    public function openPartModal(?string $taskId = null, ?string $partId = null): void
    {
        if ($partId) {
            $part = PmServiceTypePart::findOrFail($partId);
            $this->selectedPartId = $part->id;
            $this->part_task_id = $part->service_type_task_id;
            $this->part_number = $part->part_number;
            $this->part_name = $part->part_name;
            $this->part_quantity = (float) $part->quantity;
            $this->part_unit = $part->unit;
            $this->part_action_type = $part->action_type;
            $this->part_remarks = $part->remarks ?? '';
        } else {
            $this->reset(['selectedPartId', 'part_number', 'part_name', 'part_remarks']);
            $this->part_task_id = $taskId;
            $this->part_quantity = 1;
            $this->part_unit = 'Pcs';
            $this->part_action_type = 'replace';
        }

        $this->showPartModal = true;
    }

    public function savePart(): void
    {
        $this->validate([
            'part_task_id' => 'required|exists:pm_service_type_tasks,id',
            'part_number' => 'required|string|max:255',
            'part_name' => 'required|string|max:255',
            'part_quantity' => 'required|numeric|min:0.01',
            'part_unit' => 'required|string|max:50',
            'part_action_type' => 'required|in:replace,check,top_up',
        ]);

        $data = [
            'service_type_task_id' => $this->part_task_id,
            'part_number' => $this->part_number,
            'part_name' => $this->part_name,
            'quantity' => $this->part_quantity,
            'unit' => $this->part_unit,
            'action_type' => $this->part_action_type,
            'remarks' => $this->part_remarks,
            'updated_by' => Auth::id(),
        ];

        if ($this->selectedPartId) {
            PmServiceTypePart::findOrFail($this->selectedPartId)->update($data);
            session()->flash('success', 'Part berhasil diperbarui.');
        } else {
            $data['created_by'] = Auth::id();
            PmServiceTypePart::create($data);
            session()->flash('success', 'Part baru berhasil ditambahkan.');
        }

        $this->showPartModal = false;
    }

    // ── Detail View ───────────────────────────────────────────

    public function openDetailModal(string $id): void
    {
        $this->activeServiceType = PmServiceType::with(['tasks.parts'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    // ── Delete ────────────────────────────────────────────────

    public function deleteServiceType(string $id): void
    {
        PmServiceType::findOrFail($id)->delete();
        session()->flash('success', 'Service Type berhasil dihapus.');
    }

    public function deleteTask(string $id): void
    {
        PmServiceTypeTask::findOrFail($id)->delete();
        session()->flash('success', 'Task berhasil dihapus.');
    }

    public function deletePart(string $id): void
    {
        PmServiceTypePart::findOrFail($id)->delete();
        session()->flash('success', 'Part berhasil dihapus.');
    }

    // ── Render ────────────────────────────────────────────────

    public function render()
    {
        $query = PmServiceType::with(['unitModel'])->withCount('tasks')
            ->when($this->search, fn ($q) => $q->where(function ($sq) {
                $sq->where('name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            }))
            ->when($this->filterMeasurement, fn ($q) => $q->where('measurement_type', $this->filterMeasurement));

        $serviceTypes = $query->orderBy('measurement_type')->orderBy('interval_value')->paginate(15);

        // Metrics
        $totalTypes = PmServiceType::count();
        $hmTypes = PmServiceType::where('measurement_type', 'hm')->count();
        $kmTypes = PmServiceType::where('measurement_type', 'km')->count();
        $totalTasks = PmServiceTypeTask::count();

        // PM Unit Models for dropdown
        $pmUnitModels = PmUnitModel::where('status', 'active')->orderBy('name')->get();

        return view('livewire.plt.pm-service-type-page', compact(
            'serviceTypes',
            'totalTypes',
            'hmTypes',
            'kmTypes',
            'totalTasks',
            'pmUnitModels',
        ));
    }
}
