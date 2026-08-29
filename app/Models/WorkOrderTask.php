<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrderTask extends BaseModel
{
    protected $table = 'work_order_tasks';

    protected $fillable = [
        'work_order_id',
        'problem_title',
        'component',
        'reff_component_id',
        'is_primary',
        'task_order',
        'status',
        'breakdown_at',
        'ready_at',
        'downtime_hours',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'task_order' => 'integer',
        'breakdown_at' => 'datetime',
        'ready_at' => 'datetime',
        'downtime_hours' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::deleting(function (self $task) {
            $task->subtasks()->each(function (WorkOrderSubtask $subtask) {
                $subtask->delete();
            });
        });
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function reffComponent(): BelongsTo
    {
        return $this->belongsTo(ReffComponent::class, 'reff_component_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(WorkOrderSubtask::class, 'work_order_task_id')->orderBy('subtask_order');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'open' => ['label' => 'Open', 'class' => 'badge-light-primary text-primary'],
            'in_progress' => ['label' => 'In Progress', 'class' => 'badge-light-warning text-warning'],
            'waiting_part' => ['label' => 'Waiting Part', 'class' => 'badge-light-info text-info'],
            'completed' => ['label' => 'Completed', 'class' => 'badge-light-success text-success'],
            'cancelled' => ['label' => 'Cancelled', 'class' => 'badge-light-danger text-danger'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light-secondary text-gray-700'],
        };
    }
}
