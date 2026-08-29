<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrderSubtask extends BaseModel
{
    protected $table = 'work_order_subtasks';

    protected $fillable = [
        'work_order_task_id',
        'action_title',
        'subtask_order',
        'assigned_to_id',
        'status',
        'labor_hours',
        'breakdown_at',
        'ready_at',
        'obstacle',
        'obstacle_notes',
        'actual_start_time',
        'actual_end_time',
        'notes',
    ];

    protected $casts = [
        'subtask_order' => 'integer',
        'labor_hours' => 'decimal:2',
        'breakdown_at' => 'datetime',
        'ready_at' => 'datetime',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function (self $subtask) {
            $subtask->spareparts()->each(function (WorkOrderSubtaskSparepart $part) {
                $part->delete();
            });
            $subtask->mechanics()->detach();
        });
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(WorkOrderTask::class, 'work_order_task_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function mechanics(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'work_order_subtask_mechanics')
            ->withPivot('hours_spent')
            ->withTimestamps();
    }

    public function spareparts(): HasMany
    {
        return $this->hasMany(WorkOrderSubtaskSparepart::class, 'work_order_subtask_id');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending' => ['label' => 'Pending', 'class' => 'badge-light-secondary text-gray-700'],
            'in_progress' => ['label' => 'In Progress', 'class' => 'badge-light-warning text-warning'],
            'waiting_part' => ['label' => 'Waiting Part', 'class' => 'badge-light-info text-info'],
            'completed' => ['label' => 'Completed', 'class' => 'badge-light-success text-success'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light-secondary text-gray-700'],
        };
    }

    public function getObstacleBadgeAttribute(): array
    {
        return match ($this->obstacle ?? 'none') {
            'none' => ['label' => 'Lancar (Tanpa Kendala)', 'class' => 'badge-light-success text-success'],
            'waiting_part' => ['label' => 'Menunggu Sparepart', 'class' => 'badge-light-danger text-danger'],
            'waiting_manpower' => ['label' => 'Menunggu Personel/Mekanik', 'class' => 'badge-light-warning text-warning'],
            'waiting_tool' => ['label' => 'Menunggu Special Tool/Crane', 'class' => 'badge-light-info text-info'],
            'waiting_weather' => ['label' => 'Kendala Cuaca / Hujan', 'class' => 'badge-light-primary text-primary'],
            'waiting_approval' => ['label' => 'Menunggu Approval/PO', 'class' => 'badge-light-secondary text-dark'],
            'waiting_location' => ['label' => 'Kendala Akses / Lokasi', 'class' => 'badge-light-dark text-dark'],
            'waiting_external' => ['label' => 'Pihak Luar / Vendor', 'class' => 'badge-light-danger text-danger'],
            default => ['label' => ucfirst(str_replace('_', ' ', (string) $this->obstacle)), 'class' => 'badge-light-secondary text-gray-700'],
        };
    }
}
