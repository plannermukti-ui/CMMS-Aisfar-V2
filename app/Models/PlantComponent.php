<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlantComponent extends BaseModel
{
    protected $table = 'plant_components';

    protected $fillable = [
        'component_code',
        'serial_number',
        'name',
        'component_type',
        'brand_model',
        'equipment_id',
        'position',
        'status',
        'accumulated_hours',
        'target_life_hours',
        'installed_at_hm',
        'installed_date',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'accumulated_hours' => 'decimal:2',
        'target_life_hours' => 'decimal:2',
        'installed_at_hm' => 'decimal:2',
        'installed_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $comp) {
            if (empty($comp->component_code)) {
                $prefix = 'CMP-'.strtoupper(substr($comp->component_type ?? 'GEN', 0, 3)).'-';
                $last = static::where('component_code', 'LIKE', $prefix.'%')->orderBy('component_code', 'desc')->first();
                $nextSeq = 1;
                if ($last) {
                    $nextSeq = ((int) substr($last->component_code, strlen($prefix))) + 1;
                }
                $comp->component_code = $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(PlantComponentMovement::class, 'component_id')->orderBy('movement_date', 'desc');
    }

    public function conditionReports(): HasMany
    {
        return $this->hasMany(PlantCcr::class, 'component_id')->orderBy('ccr_date', 'desc');
    }

    public function outsideRepairs(): HasMany
    {
        return $this->hasMany(PlantOsr::class, 'component_id')->orderBy('order_date', 'desc');
    }

    public function failureReports(): HasMany
    {
        return $this->hasMany(PlantFar::class, 'component_id')->orderBy('incident_date', 'desc');
    }

    public function getLifePercentageAttribute(): float
    {
        if ($this->target_life_hours > 0) {
            return min(100, round(($this->accumulated_hours / $this->target_life_hours) * 100, 1));
        }

        return 0;
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'installed' => ['label' => 'Terpasang di Unit', 'class' => 'badge-light-success text-success'],
            'ready_spare' => ['label' => 'Ready di Gudang / Workshop', 'class' => 'badge-light-primary text-primary'],
            'in_repair_workshop' => ['label' => 'Perbaikan Internal', 'class' => 'badge-light-warning text-warning'],
            'in_outside_repair' => ['label' => 'Di Bengkel Luar (OSR)', 'class' => 'badge-light-info text-info'],
            'scrapped' => ['label' => 'Scrapped (Afkir)', 'class' => 'badge-light-danger text-danger'],
            default => ['label' => ucfirst(str_replace('_', ' ', $this->status)), 'class' => 'badge-light-secondary text-gray-700'],
        };
    }
}
