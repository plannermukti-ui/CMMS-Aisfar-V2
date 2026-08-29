<?php

namespace App\Models;

use App\Observers\ActivityObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([ActivityObserver::class])]
class WorkOrder extends BaseModel
{
    protected $table = 'work_orders';

    protected $fillable = [
        'wo_number',
        'wo_date',
        'breakdown_at',
        'ready_at',
        'wo_type',
        'priority',
        'status',
        'unit_status',
        'is_opportunity',
        'equipment_id',
        'site_id',
        'current_hm',
        'current_km',
        'requester_id',
        'assigned_to_id',
        'approved_by',
        'job_title',
        'problem_description',
        'action_taken',
        'root_cause',
        'scheduled_start_date',
        'scheduled_end_date',
        'actual_start_time',
        'actual_end_time',
        'downtime_hours',
        'total_labor_hours',
        'before_photo',
        'after_photo',
        'attachment_file',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'wo_date' => 'date',
        'breakdown_at' => 'datetime',
        'ready_at' => 'datetime',
        'is_opportunity' => 'boolean',
        'scheduled_start_date' => 'datetime',
        'scheduled_end_date' => 'datetime',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime',
        'current_hm' => 'decimal:2',
        'current_km' => 'decimal:2',
        'downtime_hours' => 'decimal:2',
        'total_labor_hours' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $wo) {
            if (empty($wo->wo_number)) {
                $prefix = 'WO-'.date('ym').'-';
                $last = static::where('wo_number', 'LIKE', $prefix.'%')
                    ->orderBy('wo_number', 'desc')
                    ->first();

                $nextSeq = 1;
                if ($last) {
                    $lastSeq = (int) substr($last->wo_number, strlen($prefix));
                    $nextSeq = $lastSeq + 1;
                }

                $wo->wo_number = $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
            }

            if (empty($wo->wo_date)) {
                $wo->wo_date = now()->toDateString();
            }

            if (empty($wo->breakdown_at)) {
                $wo->breakdown_at = now();
            }
        });

        static::deleting(function (self $wo) {
            $wo->tasks()->each(function (WorkOrderTask $task) {
                $task->delete();
            });
        });
    }

    public function tasks()
    {
        return $this->hasMany(WorkOrderTask::class, 'work_order_id')->orderBy('task_order');
    }

    public function comments()
    {
        return $this->hasMany(WorkOrderComment::class)->whereNull('parent_id')->with(['user', 'replies'])->orderBy('created_at', 'desc');
    }

    public function primaryTask()
    {
        return $this->hasOne(WorkOrderTask::class, 'work_order_id')->where('is_primary', true);
    }

    public function subtasks()
    {
        return $this->hasManyThrough(WorkOrderSubtask::class, WorkOrderTask::class, 'work_order_id', 'work_order_task_id');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function mechanics(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'work_order_mechanics')
            ->withPivot('hours_spent')
            ->withTimestamps();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'draft' => ['label' => 'Draft', 'class' => 'badge-light-secondary text-gray-700'],
            'open' => ['label' => 'Open', 'class' => 'badge-light-primary text-primary'],
            'in_progress' => ['label' => 'In Progress', 'class' => 'badge-light-warning text-warning'],
            'waiting_part' => ['label' => 'Waiting Part', 'class' => 'badge-light-info text-info'],
            'completed' => ['label' => 'Completed', 'class' => 'badge-light-success text-success'],
            'closed' => ['label' => 'Closed', 'class' => 'badge-light-dark text-dark'],
            'cancelled' => ['label' => 'Cancelled', 'class' => 'badge-light-danger text-danger'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light-primary text-primary'],
        };
    }

    public function getUnitStatusBadgeAttribute(): array
    {
        return match ($this->unit_status ?? 'breakdown') {
            'ready' => ['label' => 'Ready Operasi', 'class' => 'badge-light-success text-success', 'dot' => 'bg-success'],
            'breakdown' => ['label' => 'Breakdown (BD)', 'class' => 'badge-light-danger text-danger', 'dot' => 'bg-danger'],
            'in_progress' => ['label' => 'In Progress / Servis', 'class' => 'badge-light-warning text-warning', 'dot' => 'bg-warning'],
            'standby' => ['label' => 'Standby', 'class' => 'badge-light-info text-info', 'dot' => 'bg-info'],
            'scheduled_maintenance' => ['label' => 'Scheduled PM', 'class' => 'badge-light-primary text-primary', 'dot' => 'bg-primary'],
            'accident' => ['label' => 'Accident', 'class' => 'badge-light-dark text-dark', 'dot' => 'bg-dark'],
            default => ['label' => ucfirst((string) $this->unit_status), 'class' => 'badge-light-secondary text-gray-700', 'dot' => 'bg-secondary'],
        };
    }

    public function getPriorityBadgeAttribute(): array
    {
        return match ($this->priority) {
            'low' => ['label' => 'Low', 'class' => 'badge-light-success text-success'],
            'medium' => ['label' => 'Medium', 'class' => 'badge-light-info text-info'],
            'high' => ['label' => 'High', 'class' => 'badge-light-warning text-warning'],
            'emergency' => ['label' => 'Emergency', 'class' => 'badge-light-danger text-danger'],
            default => ['label' => ucfirst($this->priority), 'class' => 'badge-light-primary text-primary'],
        };
    }

    public function getTypeBadgeAttribute(): array
    {
        return match ($this->wo_type) {
            'plan', 'planned' => ['label' => 'Plan (PM Plan)', 'class' => 'bg-light-primary text-primary'],
            'preventive' => ['label' => 'Preventive (PM)', 'class' => 'bg-light-primary text-primary'],
            'corrective' => ['label' => 'Corrective (CM)', 'class' => 'bg-light-info text-info'],
            'breakdown' => ['label' => 'Breakdown (BD)', 'class' => 'bg-light-danger text-danger'],
            'inspection' => ['label' => 'Inspection', 'class' => 'bg-light-warning text-warning'],
            'overhaul' => ['label' => 'Overhaul', 'class' => 'bg-light-dark text-dark'],
            default => ['label' => ucfirst($this->wo_type), 'class' => 'bg-light-secondary text-gray-700'],
        };
    }
}
