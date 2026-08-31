<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PmServiceSchedule extends BaseModel
{
    protected $table = 'pm_service_schedules';

    protected $fillable = [
        'equipment_id',
        'service_type_id',
        'last_executed_hm_km',
        'last_executed_date',
        'next_target_hm_km',
        'next_plan_date',
        'remain_days',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'last_executed_hm_km' => 'decimal:2',
        'last_executed_date' => 'date',
        'next_target_hm_km' => 'decimal:2',
        'next_plan_date' => 'date',
        'remain_days' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(PmServiceType::class, 'service_type_id');
    }

    public function pmWorkOrders(): HasMany
    {
        return $this->hasMany(PmWorkOrder::class, 'schedule_id');
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
            'pending' => ['label' => 'Pending', 'class' => 'badge-light-secondary text-secondary'],
            'due_soon' => ['label' => 'Due Soon', 'class' => 'badge-light-warning text-warning'],
            'overdue' => ['label' => 'Overdue', 'class' => 'badge-light-danger text-danger'],
            'completed' => ['label' => 'Completed', 'class' => 'badge-light-success text-success'],
            'cancelled' => ['label' => 'Cancelled', 'class' => 'badge-light-dark text-dark'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light-secondary text-secondary'],
        };
    }

    public function getProgressPercentageAttribute(): float
    {
        if (! $this->next_target_hm_km || ! $this->last_executed_hm_km || $this->next_target_hm_km <= $this->last_executed_hm_km) {
            return 0;
        }

        $currentHm = $this->equipment?->current_hm ?? 0;
        $range = $this->next_target_hm_km - $this->last_executed_hm_km;
        $progress = $currentHm - $this->last_executed_hm_km;

        return min(max(($progress / $range) * 100, 0), 100);
    }
}
