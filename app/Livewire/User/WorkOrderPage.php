<?php

namespace App\Livewire\User;

use App\Models\Equipment;
use App\Models\MaterialOrder;
use App\Models\MaterialOrderItem;
use App\Models\Part;
use App\Models\ReffComponent;
use App\Models\Site;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderComment;
use App\Models\WorkOrderSubtask;
use App\Models\WorkOrderSubtaskSparepart;
use App\Models\WorkOrderTask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.user')]
#[Title('Plant - Work Order')]
class WorkOrderPage extends Component
{
    use WithFileUploads, WithPagination;

    protected string $paginationTheme = 'bootstrap';

    // Filters
    public string $search = '';

    public string $filterStatus = 'all';

    public string $filterType = 'all';

    public string $filterPriority = 'all';

    // Modal Visibility
    public bool $showFormModal = false;

    public bool $showDetailModal = false;

    public bool $showCompleteModal = false;

    public ?string $selectedWoId = null;

    public ?WorkOrder $selectedWorkOrder = null;

    // Form Header Fields
    public ?string $editId = null;

    public string $equipment_id = '';

    public string $site_id = '';

    public ?string $current_hm = null;

    public ?string $current_km = null;

    public string $wo_type = 'plan';

    public string $priority = 'medium';

    public string $unit_status = 'breakdown';

    public bool $is_opportunity = false;

    public string $breakdown_at = '';

    public ?string $ready_at = null;

    public ?string $scheduled_start_date = null;

    public ?string $scheduled_end_date = null;

    public $before_photo_file = null;

    public $attachment_doc_file = null;

    // Hierarchical Tasks & Subtasks & Spareparts
    public array $tasks = [];

    // Complete Work Order fields
    public string $complete_action_taken = '';

    public string $complete_root_cause = 'Wear & Tear';

    public ?string $complete_total_labor_hours = '1';

    public $after_photo_file = null;

    // Comment / Discussion fields
    public string $newComment = '';

    public ?string $replyingToId = null;

    public string $newReply = '';

    public function updatedEquipmentId($value): void
    {
        if ($value) {
            $eq = Equipment::find($value);
            if ($eq && $eq->site_id) {
                $this->site_id = (string) $eq->site_id;
            }
        }
    }

    public function updatedReadyAt($value): void
    {
        if (! empty($value) && $this->unit_status !== 'ready') {
            $this->unit_status = 'ready';
        }
    }

    public function updatedUnitStatus($value): void
    {
        if ($value === 'ready' && empty($this->ready_at)) {
            $this->ready_at = now()->format('Y-m-d\TH:i');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $defaultBreakdown = now()->format('Y-m-d\TH:i');
        $this->breakdown_at = $defaultBreakdown;
        $this->ready_at = null;
        $this->unit_status = 'breakdown';
        $this->is_opportunity = false;
        $this->wo_type = 'plan';

        $this->tasks = [
            [
                'problem_title' => '',
                'component' => '',
                'reff_component_id' => '',
                'is_primary' => true,
                'breakdown_at' => $defaultBreakdown,
                'ready_at' => '',
                'downtime_hours' => 0,
                'subtasks' => [
                    [
                        'action_title' => '',
                        'assigned_to_id' => (string) Auth::id(),
                        'mechanic_ids' => [],
                        'breakdown_at' => $defaultBreakdown,
                        'ready_at' => '',
                        'obstacle' => 'none',
                        'obstacle_notes' => '',
                        'spareparts' => [],
                    ],
                ],
            ],
        ];
        $this->showFormModal = true;
    }

    public function openEditModal(string $id): void
    {
        $this->resetForm();
        $wo = WorkOrder::with(['tasks.subtasks.mechanics', 'tasks.subtasks.spareparts'])->findOrFail($id);

        $this->editId = $wo->id;
        $this->equipment_id = (string) $wo->equipment_id;
        $this->site_id = (string) ($wo->site_id ?? '');
        $this->current_hm = $wo->current_hm ? (string) $wo->current_hm : null;
        $this->current_km = $wo->current_km ? (string) $wo->current_km : null;
        $this->wo_type = $wo->wo_type;
        $this->priority = $wo->priority;
        $this->unit_status = $wo->unit_status ?? 'breakdown';
        $this->is_opportunity = (bool) $wo->is_opportunity;
        $this->breakdown_at = $wo->breakdown_at ? $wo->breakdown_at->format('Y-m-d\TH:i') : ($wo->created_at ? $wo->created_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i'));
        $this->ready_at = $wo->ready_at ? $wo->ready_at->format('Y-m-d\TH:i') : null;
        $this->scheduled_start_date = $wo->scheduled_start_date ? $wo->scheduled_start_date->format('Y-m-d\TH:i') : null;
        $this->scheduled_end_date = $wo->scheduled_end_date ? $wo->scheduled_end_date->format('Y-m-d\TH:i') : null;

        $taskList = [];
        foreach ($wo->tasks as $tIndex => $task) {
            $subtaskList = [];
            foreach ($task->subtasks as $sIndex => $subtask) {
                $sparepartList = [];
                foreach ($subtask->spareparts as $part) {
                    $masterPart = $part->part_id ? Part::find($part->part_id) : Part::where('part_number', $part->part_number)->first();
                    $sparepartList[] = [
                        'part_id' => (string) ($part->part_id ?? ($masterPart->id ?? '')),
                        'part_number' => $part->part_number,
                        'part_name' => $part->part_name,
                        'quantity' => (float) $part->quantity,
                        'unit' => $part->unit,
                        'stock_available' => $masterPart ? (float) $masterPart->stock_on_hand : 0,
                        'action_type' => $part->action_type,
                        'status' => $part->status,
                        'source_unit' => $part->source_unit ?? '',
                    ];
                }

                $subtaskList[] = [
                    'action_title' => $subtask->action_title,
                    'assigned_to_id' => (string) ($subtask->assigned_to_id ?? ''),
                    'mechanic_ids' => $subtask->mechanics->pluck('id')->toArray(),
                    'breakdown_at' => $subtask->breakdown_at ? $subtask->breakdown_at->format('Y-m-d\TH:i') : ($task->breakdown_at ? $task->breakdown_at->format('Y-m-d\TH:i') : $this->breakdown_at),
                    'ready_at' => $subtask->ready_at ? $subtask->ready_at->format('Y-m-d\TH:i') : '',
                    'obstacle' => $subtask->obstacle ?? 'none',
                    'obstacle_notes' => $subtask->obstacle_notes ?? '',
                    'spareparts' => $sparepartList,
                ];
            }

            $taskList[] = [
                'problem_title' => $task->problem_title,
                'component' => $task->component ?? '',
                'reff_component_id' => $task->reff_component_id ? (string) $task->reff_component_id : '',
                'is_primary' => (bool) $task->is_primary,
                'breakdown_at' => $task->breakdown_at ? $task->breakdown_at->format('Y-m-d\TH:i') : $this->breakdown_at,
                'ready_at' => $task->ready_at ? $task->ready_at->format('Y-m-d\TH:i') : '',
                'downtime_hours' => (float) ($task->downtime_hours ?? 0),
                'subtasks' => ! empty($subtaskList) ? $subtaskList : [
                    [
                        'action_title' => '',
                        'assigned_to_id' => (string) Auth::id(),
                        'mechanic_ids' => [],
                        'breakdown_at' => $this->breakdown_at,
                        'ready_at' => '',
                        'obstacle' => 'none',
                        'obstacle_notes' => '',
                        'spareparts' => [],
                    ],
                ],
            ];
        }

        if (empty($taskList)) {
            $taskList = [
                [
                    'problem_title' => $wo->problem_description ?? $wo->job_title,
                    'component' => '',
                    'reff_component_id' => '',
                    'is_primary' => true,
                    'breakdown_at' => $this->breakdown_at,
                    'ready_at' => $this->ready_at ?: '',
                    'downtime_hours' => 0,
                    'subtasks' => [
                        [
                            'action_title' => $wo->job_title,
                            'assigned_to_id' => (string) ($wo->assigned_to_id ?? Auth::id()),
                            'mechanic_ids' => [],
                            'breakdown_at' => $this->breakdown_at,
                            'ready_at' => $this->ready_at ?: '',
                            'obstacle' => 'none',
                            'obstacle_notes' => '',
                            'spareparts' => [],
                        ],
                    ],
                ],
            ];
        }

        $this->tasks = $taskList;
        $this->showFormModal = true;
    }

    public function addTask(): void
    {
        $this->tasks[] = [
            'problem_title' => '',
            'component' => '',
            'reff_component_id' => '',
            'is_primary' => false,
            'breakdown_at' => $this->breakdown_at ?: now()->format('Y-m-d\TH:i'),
            'ready_at' => '',
            'downtime_hours' => 0,
            'subtasks' => [
                [
                    'action_title' => '',
                    'assigned_to_id' => (string) Auth::id(),
                    'mechanic_ids' => [],
                    'breakdown_at' => $this->breakdown_at ?: now()->format('Y-m-d\TH:i'),
                    'ready_at' => '',
                    'obstacle' => 'none',
                    'obstacle_notes' => '',
                    'spareparts' => [],
                ],
            ],
        ];
    }

    public function removeTask(int|string $taskIndex): void
    {
        $taskIndex = (int) $taskIndex;
        if (count($this->tasks) > 1) {
            unset($this->tasks[$taskIndex]);
            $this->tasks = array_values($this->tasks);
            $this->syncPrimaryTask();
        }
    }

    public function moveTaskUp(int|string $taskIndex): void
    {
        $taskIndex = (int) $taskIndex;
        if ($taskIndex > 0 && isset($this->tasks[$taskIndex])) {
            $temp = $this->tasks[$taskIndex - 1];
            $this->tasks[$taskIndex - 1] = $this->tasks[$taskIndex];
            $this->tasks[$taskIndex] = $temp;
            $this->syncPrimaryTask();
        }
    }

    public function moveTaskDown(int|string $taskIndex): void
    {
        $taskIndex = (int) $taskIndex;
        if ($taskIndex < count($this->tasks) - 1 && isset($this->tasks[$taskIndex])) {
            $temp = $this->tasks[$taskIndex + 1];
            $this->tasks[$taskIndex + 1] = $this->tasks[$taskIndex];
            $this->tasks[$taskIndex] = $temp;
            $this->syncPrimaryTask();
        }
    }

    protected function syncPrimaryTask(): void
    {
        foreach ($this->tasks as $idx => &$task) {
            $task['is_primary'] = ($idx === 0);
        }
    }

    public function addSubtask(int|string $taskIndex): void
    {
        $taskIndex = (int) $taskIndex;
        $parentBreakdown = $this->tasks[$taskIndex]['breakdown_at'] ?? ($this->breakdown_at ?: now()->format('Y-m-d\TH:i'));
        $this->tasks[$taskIndex]['subtasks'][] = [
            'action_title' => '',
            'assigned_to_id' => (string) Auth::id(),
            'mechanic_ids' => [],
            'breakdown_at' => $parentBreakdown,
            'ready_at' => '',
            'obstacle' => 'none',
            'obstacle_notes' => '',
            'spareparts' => [],
        ];
    }

    public function removeSubtask(int|string $taskIndex, int|string $subIndex): void
    {
        $taskIndex = (int) $taskIndex;
        $subIndex = (int) $subIndex;
        if (count($this->tasks[$taskIndex]['subtasks']) > 1) {
            unset($this->tasks[$taskIndex]['subtasks'][$subIndex]);
            $this->tasks[$taskIndex]['subtasks'] = array_values($this->tasks[$taskIndex]['subtasks']);
        }
    }

    public function moveSubtaskUp(int|string $taskIndex, int|string $subIndex): void
    {
        $taskIndex = (int) $taskIndex;
        $subIndex = (int) $subIndex;
        if (isset($this->tasks[$taskIndex]['subtasks'][$subIndex]) && $subIndex > 0) {
            $temp = $this->tasks[$taskIndex]['subtasks'][$subIndex - 1];
            $this->tasks[$taskIndex]['subtasks'][$subIndex - 1] = $this->tasks[$taskIndex]['subtasks'][$subIndex];
            $this->tasks[$taskIndex]['subtasks'][$subIndex] = $temp;
        }
    }

    public function moveSubtaskDown(int|string $taskIndex, int|string $subIndex): void
    {
        $taskIndex = (int) $taskIndex;
        $subIndex = (int) $subIndex;
        if (isset($this->tasks[$taskIndex]['subtasks'][$subIndex]) && $subIndex < count($this->tasks[$taskIndex]['subtasks']) - 1) {
            $temp = $this->tasks[$taskIndex]['subtasks'][$subIndex + 1];
            $this->tasks[$taskIndex]['subtasks'][$subIndex + 1] = $this->tasks[$taskIndex]['subtasks'][$subIndex];
            $this->tasks[$taskIndex]['subtasks'][$subIndex] = $temp;
        }
    }

    public function moveSubtaskToTask(int|string $fromTaskIndex, int|string $subIndex, int|string $toTaskIndex): void
    {
        $fromTaskIndex = (int) $fromTaskIndex;
        $subIndex = (int) $subIndex;
        $toTaskIndex = (int) $toTaskIndex;

        if ($fromTaskIndex === $toTaskIndex) {
            return;
        }

        if (isset($this->tasks[$fromTaskIndex]['subtasks'][$subIndex]) && isset($this->tasks[$toTaskIndex])) {
            $subtask = $this->tasks[$fromTaskIndex]['subtasks'][$subIndex];

            // Remove from source task
            unset($this->tasks[$fromTaskIndex]['subtasks'][$subIndex]);
            $this->tasks[$fromTaskIndex]['subtasks'] = array_values($this->tasks[$fromTaskIndex]['subtasks']);

            // Ensure source task still has at least 1 subtask
            if (empty($this->tasks[$fromTaskIndex]['subtasks'])) {
                $this->tasks[$fromTaskIndex]['subtasks'][] = [
                    'action_title' => '',
                    'assigned_to_id' => (string) Auth::id(),
                    'mechanic_ids' => [],
                    'breakdown_at' => $this->tasks[$fromTaskIndex]['breakdown_at'] ?? $this->breakdown_at,
                    'ready_at' => '',
                    'obstacle' => 'none',
                    'obstacle_notes' => '',
                    'spareparts' => [],
                ];
            }

            // Append to destination task
            $this->tasks[$toTaskIndex]['subtasks'][] = $subtask;
        }
    }

    public function addSparepart(int|string $taskIndex, int|string $subIndex): void
    {
        $taskIndex = (int) $taskIndex;
        $subIndex = (int) $subIndex;
        if (isset($this->tasks[$taskIndex]['subtasks'][$subIndex])) {
            $this->tasks[$taskIndex]['subtasks'][$subIndex]['spareparts'][] = [
                'part_id' => '',
                'part_number' => '',
                'part_name' => '',
                'quantity' => 1,
                'unit' => 'PCS',
                'stock_available' => 0,
                'action_type' => 'replace',
                'status' => 'installed',
                'source_unit' => '',
            ];
        }
    }

    public function removeSparepart(int|string $taskIndex, int|string $subIndex, int|string $partIndex): void
    {
        $taskIndex = (int) $taskIndex;
        $subIndex = (int) $subIndex;
        $partIndex = (int) $partIndex;
        if (isset($this->tasks[$taskIndex]['subtasks'][$subIndex]['spareparts'][$partIndex])) {
            unset($this->tasks[$taskIndex]['subtasks'][$subIndex]['spareparts'][$partIndex]);
            $this->tasks[$taskIndex]['subtasks'][$subIndex]['spareparts'] = array_values($this->tasks[$taskIndex]['subtasks'][$subIndex]['spareparts']);
        }
    }

    public function updatedTasks(mixed $value, string $key): void
    {
        // Check if updating part_id: e.g. "0.subtasks.0.spareparts.0.part_id"
        if (str_ends_with($key, '.part_id') && ! empty($value)) {
            $parts = explode('.', $key);
            if (count($parts) >= 5 && $parts[1] === 'subtasks' && $parts[3] === 'spareparts') {
                $tIdx = (int) $parts[0];
                $sIdx = (int) $parts[2];
                $pIdx = (int) $parts[4];

                $masterPart = Part::find($value);
                if ($masterPart && isset($this->tasks[$tIdx]['subtasks'][$sIdx]['spareparts'][$pIdx])) {
                    $this->tasks[$tIdx]['subtasks'][$sIdx]['spareparts'][$pIdx]['part_number'] = $masterPart->part_number;
                    $this->tasks[$tIdx]['subtasks'][$sIdx]['spareparts'][$pIdx]['part_name'] = $masterPart->name;
                    $this->tasks[$tIdx]['subtasks'][$sIdx]['spareparts'][$pIdx]['unit'] = $masterPart->uom ?? 'PCS';
                    $this->tasks[$tIdx]['subtasks'][$sIdx]['spareparts'][$pIdx]['stock_available'] = (float) $masterPart->stock_on_hand;
                }
            }
        }
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->equipment_id = '';
        $this->site_id = '';
        $this->current_hm = null;
        $this->current_km = null;
        $this->wo_type = 'plan';
        $this->priority = 'medium';
        $this->unit_status = 'breakdown';
        $this->is_opportunity = false;
        $this->breakdown_at = now()->format('Y-m-d\TH:i');
        $this->ready_at = null;
        $this->scheduled_start_date = null;
        $this->scheduled_end_date = null;
        $this->before_photo_file = null;
        $this->attachment_doc_file = null;
        $this->tasks = [];
    }

    public function saveWorkOrder(): void
    {
        $this->validate([
            'equipment_id' => 'required|exists:equipments,id',
            'wo_type' => 'required|in:plan,preventive,corrective,breakdown,inspection,overhaul',
            'priority' => 'required|in:low,medium,high,emergency',
            'unit_status' => 'required|in:ready,breakdown,in_progress,standby,scheduled_maintenance,accident',
            'breakdown_at' => 'required|date',
            'ready_at' => 'nullable|date',
            'tasks.0.problem_title' => 'required|min:3',
            'tasks.0.subtasks.0.action_title' => 'required|min:3',
            'before_photo_file' => 'nullable|image|max:10240',
            'attachment_doc_file' => 'nullable|file|max:20480',
        ], [
            'equipment_id.required' => 'Unit Alat / Equipment wajib dipilih.',
            'breakdown_at.required' => 'Waktu Breakdown (Date & Time) wajib diisi.',
            'tasks.0.problem_title.required' => 'Problem utama (Primary Task) wajib diisi.',
            'tasks.0.subtasks.0.action_title.required' => 'Action pertama wajib diisi.',
        ]);

        // Business Rule 1: Status Head cannot be Ready if ready_at is empty
        if ($this->unit_status === 'ready' && empty($this->ready_at)) {
            throw ValidationException::withMessages([
                'ready_at' => 'Status Unit tidak boleh "Ready" jika Tanggal & Jam Ready (Selesai) pada Head masih kosong.',
            ]);
        }

        // Business Rule 2: Head ready_at cannot be earlier than breakdown_at
        if (! empty($this->ready_at) && strtotime($this->ready_at) < strtotime($this->breakdown_at)) {
            throw ValidationException::withMessages([
                'ready_at' => 'Tanggal & Jam Ready Unit ('.$this->ready_at.') tidak boleh lebih awal dari Tanggal & Jam Breakdown ('.$this->breakdown_at.').',
            ]);
        }

        // Business Rule 3: Task breakdown_at cannot be earlier than Head breakdown_at
        foreach ($this->tasks as $tIdx => $tData) {
            $taskNo = $tIdx + 1;
            if (! empty($tData['breakdown_at']) && strtotime($tData['breakdown_at']) < strtotime($this->breakdown_at)) {
                throw ValidationException::withMessages([
                    "tasks.{$tIdx}.breakdown_at" => "Waktu Breakdown Problem #{$taskNo} ({$tData['breakdown_at']}) tidak boleh lebih awal dari Waktu Breakdown Head ({$this->breakdown_at}).",
                ]);
            }
            if (! empty($tData['ready_at']) && ! empty($tData['breakdown_at']) && strtotime($tData['ready_at']) < strtotime($tData['breakdown_at'])) {
                throw ValidationException::withMessages([
                    "tasks.{$tIdx}.ready_at" => "Waktu Ready Problem #{$taskNo} tidak boleh lebih awal dari Waktu Breakdown Problem tersebut.",
                ]);
            }
        }

        DB::transaction(function () {
            $primaryProblem = $this->tasks[0]['problem_title'] ?? 'Maintenance';
            $primaryAction = $this->tasks[0]['subtasks'][0]['action_title'] ?? 'Repair';
            $jobTitle = $primaryProblem.' - '.$primaryAction;

            $downtimeHours = 0;
            if (! empty($this->ready_at) && ! empty($this->breakdown_at)) {
                $downtimeHours = max(0, round((strtotime($this->ready_at) - strtotime($this->breakdown_at)) / 3600, 2));
            }

            $data = [
                'equipment_id' => $this->equipment_id,
                'site_id' => $this->site_id ?: null,
                'current_hm' => $this->current_hm ? (float) $this->current_hm : null,
                'current_km' => $this->current_km ? (float) $this->current_km : null,
                'wo_type' => $this->wo_type,
                'priority' => $this->priority,
                'unit_status' => $this->unit_status,
                'is_opportunity' => $this->is_opportunity,
                'breakdown_at' => $this->breakdown_at,
                'ready_at' => $this->ready_at ?: null,
                'downtime_hours' => $downtimeHours,
                'job_title' => $jobTitle,
                'problem_description' => $primaryProblem,
                'scheduled_start_date' => $this->scheduled_start_date ?: null,
                'scheduled_end_date' => $this->scheduled_end_date ?: null,
                'updated_by' => Auth::id(),
            ];

            if ($this->unit_status === 'ready') {
                $data['status'] = 'completed';
            }

            if ($this->before_photo_file) {
                $data['before_photo'] = $this->before_photo_file->store('work-orders/photos', 'public');
            }

            if ($this->attachment_doc_file) {
                $data['attachment_file'] = $this->attachment_doc_file->store('work-orders/attachments', 'public');
            }

            if ($this->editId) {
                $wo = WorkOrder::findOrFail($this->editId);
                $wo->update($data);
                // Delete previous tasks for clean overwrite
                $wo->tasks()->each(function (WorkOrderTask $task) {
                    $task->subtasks()->each(function (WorkOrderSubtask $subtask) {
                        $subtask->spareparts()->forceDelete();
                        $subtask->mechanics()->detach();
                        $subtask->forceDelete();
                    });
                    $task->forceDelete();
                });
            } else {
                $data['requester_id'] = Auth::id();
                $data['created_by'] = Auth::id();
                $data['status'] = $this->unit_status === 'ready' ? 'completed' : 'open';
                $wo = WorkOrder::create($data);
            }

            // Sync equipment status in fleet table
            $equip = Equipment::find($this->equipment_id);
            if ($equip) {
                $equip->update(['status' => $this->unit_status]);
            }

            $shortagePartsForMol = [];

            // Create Hierarchical Tasks, Subtasks & Spareparts
            foreach ($this->tasks as $tIdx => $tData) {
                if (empty(trim($tData['problem_title'] ?? ''))) {
                    continue;
                }

                $taskDowntime = 0;
                $taskBreakdown = ! empty($tData['breakdown_at']) ? $tData['breakdown_at'] : $this->breakdown_at;
                $taskReady = ! empty($tData['ready_at']) ? $tData['ready_at'] : null;
                if ($taskReady && $taskBreakdown) {
                    $taskDowntime = max(0, round((strtotime($taskReady) - strtotime($taskBreakdown)) / 3600, 2));
                }

                $task = WorkOrderTask::create([
                    'work_order_id' => $wo->id,
                    'problem_title' => $tData['problem_title'],
                    'component' => ! empty($tData['component']) ? $tData['component'] : null,
                    'reff_component_id' => ! empty($tData['reff_component_id']) ? $tData['reff_component_id'] : null,
                    'is_primary' => $tIdx === 0,
                    'task_order' => $tIdx + 1,
                    'breakdown_at' => $taskBreakdown,
                    'ready_at' => $taskReady,
                    'downtime_hours' => $taskDowntime,
                    'status' => $taskReady ? 'completed' : 'open',
                ]);

                foreach ($tData['subtasks'] ?? [] as $sIdx => $sData) {
                    if (empty(trim($sData['action_title'] ?? ''))) {
                        continue;
                    }

                    $subtaskBreakdown = ! empty($sData['breakdown_at']) ? $sData['breakdown_at'] : $taskBreakdown;
                    $subtaskReady = ! empty($sData['ready_at']) ? $sData['ready_at'] : null;

                    $subtask = WorkOrderSubtask::create([
                        'work_order_task_id' => $task->id,
                        'action_title' => $sData['action_title'],
                        'subtask_order' => $sIdx + 1,
                        'assigned_to_id' => ! empty($sData['assigned_to_id']) ? $sData['assigned_to_id'] : null,
                        'breakdown_at' => $subtaskBreakdown,
                        'ready_at' => $subtaskReady,
                        'obstacle' => $sData['obstacle'] ?? 'none',
                        'obstacle_notes' => $sData['obstacle_notes'] ?? null,
                        'status' => $subtaskReady ? 'completed' : 'pending',
                    ]);

                    if (! empty($sData['mechanic_ids'])) {
                        $subtask->mechanics()->sync($sData['mechanic_ids']);
                    }

                    foreach ($sData['spareparts'] ?? [] as $pData) {
                        if (! empty(trim($pData['part_number'] ?? '')) || ! empty(trim($pData['part_name'] ?? ''))) {
                            $partId = ! empty($pData['part_id']) ? $pData['part_id'] : null;
                            $masterPart = $partId ? Part::find($partId) : null;
                            $stockOnHand = $masterPart ? (float) $masterPart->stock_on_hand : 0;
                            $qtyNeeded = ! empty($pData['quantity']) ? (float) $pData['quantity'] : 1;

                            $isOutOfStock = ($masterPart && $stockOnHand < $qtyNeeded) || ($pData['status'] ?? '') === 'waiting_part';
                            $partStatus = $isOutOfStock ? 'waiting_part' : ($pData['status'] ?? 'installed');

                            WorkOrderSubtaskSparepart::create([
                                'work_order_subtask_id' => $subtask->id,
                                'part_id' => $partId,
                                'part_number' => $pData['part_number'] ?: '-',
                                'part_name' => $pData['part_name'] ?: 'Sparepart',
                                'quantity' => $qtyNeeded,
                                'unit' => $pData['unit'] ?? 'Pcs',
                                'action_type' => $pData['action_type'] ?? 'replace',
                                'status' => $partStatus,
                                'source_unit' => $pData['source_unit'] ?? null,
                            ]);

                            // If stock is shortage / waiting part, prepare auto MOL in SCM
                            if ($isOutOfStock && ($pData['action_type'] ?? 'replace') !== 'swap') {
                                $shortagePartsForMol[] = [
                                    'part_id' => $partId,
                                    'part_number' => $pData['part_number'] ?: ($masterPart->part_number ?? '-'),
                                    'part_name' => $pData['part_name'] ?: ($masterPart->name ?? 'Sparepart'),
                                    'qty_requested' => $qtyNeeded,
                                    'status' => 'out_of_stock',
                                ];
                            }
                        }
                    }
                }
            }

            // Auto-Generate MOL in SCM if there are shortage parts
            if (! empty($shortagePartsForMol)) {
                $mol = MaterialOrder::create([
                    'work_order_id' => $wo->id,
                    'requester_id' => Auth::id(),
                    'status' => 'submitted',
                    'notes' => 'Auto-generated MOL dari Work Order '.$wo->wo_number.' (Unit: '.($wo->equipment->unit ?? 'Unit').')',
                ]);

                foreach ($shortagePartsForMol as $shortPart) {
                    MaterialOrderItem::create([
                        'material_order_id' => $mol->id,
                        'part_id' => $shortPart['part_id'],
                        'part_number' => $shortPart['part_number'],
                        'part_name' => $shortPart['part_name'],
                        'qty_requested' => $shortPart['qty_requested'],
                        'status' => 'out_of_stock',
                    ]);
                }
            }
        });

        $this->showFormModal = false;
        $this->resetForm();
    }

    public function openDetailModal(string $id): void
    {
        $this->selectedWoId = $id;
        $this->newComment = '';
        $this->replyingToId = null;
        $this->newReply = '';
        $this->selectedWorkOrder = WorkOrder::with([
            'equipment.reffEquip',
            'site',
            'requester',
            'assignedTo',
            'tasks.subtasks.mechanics',
            'tasks.subtasks.spareparts',
            'comments.replies.user',
        ])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function openCompleteModal(string $id): void
    {
        $this->selectedWoId = $id;
        $wo = WorkOrder::findOrFail($id);
        $this->complete_action_taken = $wo->action_taken ?? '';
        $this->complete_root_cause = $wo->root_cause ?? 'Wear & Tear';
        $this->complete_total_labor_hours = $wo->total_labor_hours ? (string) $wo->total_labor_hours : '1';
        $this->after_photo_file = null;
        $this->showCompleteModal = true;
    }

    public function deleteWorkOrder(string $id): void
    {
        $wo = WorkOrder::findOrFail($id);
        $wo->delete(); // This automatically cascades to tasks, subtasks, mechanics, and spareparts via model deleting event!

        if ($this->showDetailModal && $this->selectedWoId === $id) {
            $this->showDetailModal = false;
            $this->selectedWorkOrder = null;
        }
    }

    public function startWork(string $id): void
    {
        $wo = WorkOrder::findOrFail($id);
        $wo->update([
            'status' => 'in_progress',
            'actual_start_time' => $wo->actual_start_time ?? now(),
            'updated_by' => Auth::id(),
        ]);

        if ($this->showDetailModal && $this->selectedWorkOrder) {
            $this->selectedWorkOrder->refresh();
        }
    }

    public function submitCompleteWork(): void
    {
        $this->validate([
            'complete_action_taken' => 'required|min:3',
            'after_photo_file' => 'nullable|image|max:10240',
        ], [
            'complete_action_taken.required' => 'Tindakan perbaikan wajib diisi.',
        ]);

        if (! $this->selectedWoId) {
            return;
        }

        $wo = WorkOrder::findOrFail($this->selectedWoId);
        $readyAt = $wo->ready_at ?? now();
        $breakdownAt = $wo->breakdown_at ?? $wo->created_at ?? now();
        $downtimeHours = max(0, round((strtotime((string) $readyAt) - strtotime((string) $breakdownAt)) / 3600, 2));

        $updateData = [
            'status' => 'completed',
            'unit_status' => 'ready',
            'ready_at' => $readyAt,
            'downtime_hours' => $downtimeHours,
            'action_taken' => $this->complete_action_taken,
            'root_cause' => $this->complete_root_cause,
            'total_labor_hours' => $this->complete_total_labor_hours ? (float) $this->complete_total_labor_hours : null,
            'actual_end_time' => now(),
            'updated_by' => Auth::id(),
        ];

        if ($this->after_photo_file) {
            $updateData['after_photo'] = $this->after_photo_file->store('work-orders/photos', 'public');
        }

        $wo->update($updateData);

        // Sync equipment status
        if ($wo->equipment) {
            $wo->equipment->update(['status' => 'ready']);
        }

        // Mark all tasks & subtasks completed and set ready_at if empty
        $wo->tasks()->whereNull('ready_at')->update(['ready_at' => $readyAt, 'status' => 'completed']);
        foreach ($wo->tasks as $t) {
            $t->subtasks()->whereNull('ready_at')->update(['ready_at' => $readyAt, 'status' => 'completed']);
        }

        $this->showCompleteModal = false;
        if ($this->showDetailModal && $this->selectedWorkOrder) {
            $this->selectedWorkOrder->refresh();
        }
    }

    public function closeWorkOrder(string $id): void
    {
        $wo = WorkOrder::findOrFail($id);
        $wo->update([
            'status' => 'closed',
            'approved_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        if ($this->showDetailModal && $this->selectedWorkOrder) {
            $this->selectedWorkOrder->refresh();
        }
    }

    public function postComment(): void
    {
        $this->validate(['newComment' => 'required|min:1|max:2000'], [
            'newComment.required' => 'Komentar tidak boleh kosong.',
        ]);

        if (! $this->selectedWoId) {
            return;
        }

        WorkOrderComment::create([
            'work_order_id' => $this->selectedWoId,
            'parent_id' => null,
            'user_id' => Auth::id(),
            'body' => trim($this->newComment),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        $this->newComment = '';
        $this->selectedWorkOrder = WorkOrder::with([
            'equipment.reffEquip', 'site', 'requester', 'assignedTo',
            'tasks.subtasks.mechanics', 'tasks.subtasks.spareparts', 'comments.replies',
        ])->findOrFail($this->selectedWoId);
    }

    public function startReply(string $commentId): void
    {
        $this->replyingToId = $this->replyingToId === $commentId ? null : $commentId;
        $this->newReply = '';
    }

    public function postReply(): void
    {
        $this->validate(['newReply' => 'required|min:1|max:2000'], [
            'newReply.required' => 'Balasan tidak boleh kosong.',
        ]);

        if (! $this->selectedWoId || ! $this->replyingToId) {
            return;
        }

        WorkOrderComment::create([
            'work_order_id' => $this->selectedWoId,
            'parent_id' => $this->replyingToId,
            'user_id' => Auth::id(),
            'body' => trim($this->newReply),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        $this->newReply = '';
        $this->replyingToId = null;
        $this->selectedWorkOrder = WorkOrder::with([
            'equipment.reffEquip', 'site', 'requester', 'assignedTo',
            'tasks.subtasks.mechanics', 'tasks.subtasks.spareparts', 'comments.replies',
        ])->findOrFail($this->selectedWoId);
    }

    public function deleteComment(string $commentId): void
    {
        $comment = WorkOrderComment::findOrFail($commentId);
        if ($comment->user_id === Auth::id() || Auth::user()?->hasRole('admin')) {
            $comment->delete();
        }

        if ($this->selectedWoId) {
            $this->selectedWorkOrder = WorkOrder::with([
                'equipment.reffEquip', 'site', 'requester', 'assignedTo',
                'tasks.subtasks.mechanics', 'tasks.subtasks.spareparts', 'comments.replies',
            ])->findOrFail($this->selectedWoId);
        }
    }

    public function getMetricsProperty(): array
    {
        return [
            'total' => WorkOrder::count(),
            'in_progress' => WorkOrder::where('status', 'in_progress')->count(),
            'breakdown' => WorkOrder::where('wo_type', 'breakdown')->whereNotIn('status', ['completed', 'closed'])->count(),
            'completed' => WorkOrder::whereIn('status', ['completed', 'closed'])->count(),
        ];
    }

    public function render()
    {
        $query = WorkOrder::with([
            'equipment.reffEquip',
            'site',
            'requester',
            'tasks.subtasks.assignedTo.position',
            'tasks.subtasks.mechanics.position',
            'tasks.subtasks.spareparts.part',
        ])
            ->when($this->search, function ($q) {
                $term = '%'.strtolower(trim($this->search)).'%';
                $q->where(function ($sub) use ($term) {
                    $sub->whereRaw('LOWER(wo_number) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(job_title) LIKE ?', [$term])
                        ->orWhereHas('equipment', function ($eq) use ($term) {
                            $eq->whereRaw('LOWER(unit) LIKE ?', [$term])
                                ->orWhereRaw('LOWER(no) LIKE ?', [$term]);
                        })
                        ->orWhereHas('tasks', function ($t) use ($term) {
                            $t->whereRaw('LOWER(problem_title) LIKE ?', [$term])
                                ->orWhereHas('subtasks', function ($st) use ($term) {
                                    $st->whereRaw('LOWER(action_title) LIKE ?', [$term]);
                                });
                        });
                });
            })
            ->when($this->filterStatus !== 'all', function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->when($this->filterType !== 'all', function ($q) {
                $q->where('wo_type', $this->filterType);
            })
            ->when($this->filterPriority !== 'all', function ($q) {
                $q->where('priority', $this->filterPriority);
            })
            ->orderBy('created_at', 'desc');

        $workOrders = $query->paginate(10);
        $equipments = Equipment::with(['reffEquip', 'site'])->orderBy('unit')->get();
        $sites = Site::orderBy('site_name')->get();
        $users = User::with(['position', 'department'])->orderBy('full_name')->get();
        $masterParts = Part::with(['locations.site'])->where('is_active', true)->orderBy('name')->get();

        $selectedEquipment = ! empty($this->equipment_id) ? Equipment::with('reffEquip')->find($this->equipment_id) : null;
        $equipType = $selectedEquipment?->reffEquip?->tipe;
        $availableComponents = ReffComponent::forEquipmentType($equipType)
            ->where('status', 'Active')
            ->orderBy('sort_order')
            ->get();

        return view('livewire.user.work-order-page', [
            'workOrders' => $workOrders,
            'equipments' => $equipments,
            'sites' => $sites,
            'users' => $users,
            'masterParts' => $masterParts,
            'availableComponents' => $availableComponents,
            'metrics' => $this->metrics,
        ]);
    }
}
