<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PmServiceType extends BaseModel
{
    protected $table = 'pm_service_types';

    protected $fillable = [
        'name',
        'measurement_type',
        'interval_value',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'interval_value' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function unitModel(): BelongsTo
    {
        return $this->belongsTo(PmUnitModel::class, 'pm_unit_model_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(PmServiceTypeTask::class, 'service_type_id')->orderBy('task_order');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(PmServiceSchedule::class, 'service_type_id');
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

    public function getIntervalLabelAttribute(): string
    {
        $unit = $this->measurement_type === 'hm' ? 'Jam HM' : 'KM';

        return "{$this->interval_value} {$unit}";
    }

    public function getMeasurementTypeBadgeAttribute(): array
    {
        return match ($this->measurement_type) {
            'hm' => ['label' => 'HM', 'class' => 'badge-light-primary text-primary'],
            'km' => ['label' => 'KM', 'class' => 'badge-light-info text-info'],
            default => ['label' => strtoupper($this->measurement_type), 'class' => 'badge-light-secondary text-secondary'],
        };
    }
}
