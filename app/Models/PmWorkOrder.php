<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PmWorkOrder extends BaseModel
{
    protected $table = 'pm_work_orders';

    protected $fillable = [
        'schedule_id',
        'work_order_id',
        'hm_km_at_execution',
        'execution_date',
        'notes',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'hm_km_at_execution' => 'decimal:2',
        'execution_date' => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(PmServiceSchedule::class, 'schedule_id');
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ── Accessors ─────────────────────────────────────────────

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'generated' => ['label' => 'Generated', 'class' => 'badge-light-primary text-primary'],
            'in_progress' => ['label' => 'In Progress', 'class' => 'badge-light-warning text-warning'],
            'completed' => ['label' => 'Completed', 'class' => 'badge-light-success text-success'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light-secondary text-secondary'],
        };
    }
}
