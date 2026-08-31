<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PmUnitModel extends BaseModel
{
    protected $table = 'pm_unit_models';

    protected $fillable = [
        'name',
        'reff_equip_id',
        'measurement_type',
        'target_usage_per_day',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'target_usage_per_day' => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function reffEquip(): BelongsTo
    {
        return $this->belongsTo(ReffEquip::class, 'reff_equip_id');
    }

    public function equipments(): HasMany
    {
        return $this->hasMany(Equipment::class, 'pm_unit_model_id');
    }

    public function serviceTypes(): HasMany
    {
        return $this->hasMany(PmServiceType::class, 'pm_unit_model_id');
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

    public function getMeasurementTypeBadgeAttribute(): array
    {
        return match ($this->measurement_type) {
            'hm' => ['label' => 'Hour Meter (HM)', 'class' => 'badge-light-primary text-primary'],
            'km' => ['label' => 'Kilometer (KM)', 'class' => 'badge-light-info text-info'],
            default => ['label' => strtoupper($this->measurement_type), 'class' => 'badge-light-secondary text-secondary'],
        };
    }
}
