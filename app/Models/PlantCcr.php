<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlantCcr extends BaseModel
{
    protected $table = 'plant_component_condition_reports';

    protected $fillable = [
        'ccr_number',
        'ccr_date',
        'equipment_id',
        'component_id',
        'component_name',
        'current_unit_hm',
        'component_running_hours',
        'wear_percentage',
        'physical_condition',
        'leakage_status',
        'noise_vibration_status',
        'oil_contamination_status',
        'findings_description',
        'recommendation',
        'estimated_remaining_hours',
        'inspector_id',
        'attachment_photos',
        'status',
        'action_taken',
        'work_order_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'ccr_date' => 'date',
        'current_unit_hm' => 'decimal:2',
        'component_running_hours' => 'decimal:2',
        'wear_percentage' => 'decimal:2',
        'estimated_remaining_hours' => 'decimal:2',
        'attachment_photos' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $ccr) {
            if (empty($ccr->ccr_number)) {
                $monthYear = Carbon::parse($ccr->ccr_date ?? now())->format('ym');
                $prefix = 'CCR-'.$monthYear.'-';
                $last = static::where('ccr_number', 'LIKE', $prefix.'%')->orderBy('ccr_number', 'desc')->first();
                $nextSeq = 1;
                if ($last) {
                    $nextSeq = ((int) substr($last->ccr_number, strlen($prefix))) + 1;
                }
                $ccr->ccr_number = $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(PlantComponent::class, 'component_id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function getRecommendationBadgeAttribute(): array
    {
        return match ($this->recommendation) {
            'continue_run' => ['label' => 'Lanjut Beroperasi (Good)', 'class' => 'badge-light-success text-success'],
            'monitor_next_service' => ['label' => 'Pantau Servis Berikutnya', 'class' => 'badge-light-info text-info'],
            'schedule_changeout' => ['label' => 'Jadwalkan Ganti (Planned)', 'class' => 'badge-light-warning text-warning'],
            'immediate_replace' => ['label' => 'Segera Ganti (Urgent)', 'class' => 'badge-light-danger text-danger'],
            'rebuild_overhaul' => ['label' => 'Rekondisi / Overhaul', 'class' => 'badge-light-primary text-primary'],
            'scrap' => ['label' => 'Afkir (Scrap)', 'class' => 'badge-light-dark text-dark'],
            default => ['label' => ucfirst(str_replace('_', ' ', $this->recommendation)), 'class' => 'badge-light-secondary text-gray-700'],
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'draft' => ['label' => 'Draft', 'class' => 'badge-light-secondary text-gray-600'],
            'submitted' => ['label' => 'Terkirim (Submitted)', 'class' => 'badge-light-primary text-primary'],
            'reviewed_planner' => ['label' => 'Ditinjau Planner', 'class' => 'badge-light-warning text-warning'],
            'approved' => ['label' => 'Disetujui', 'class' => 'badge-light-success text-success'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light-secondary text-gray-600'],
        };
    }
}
