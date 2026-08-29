<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlantFar extends BaseModel
{
    protected $table = 'plant_failure_analysis_reports';

    protected $fillable = [
        'far_number',
        'incident_date',
        'equipment_id',
        'component_id',
        'work_order_id',
        'investigator_id',
        'unit_hm_at_failure',
        'component_hm_at_failure',
        'failure_type',
        'failure_title',
        'problem_statement',
        'failure_symptoms',
        'root_cause_5why',
        'fishbone_factors',
        'root_cause_summary',
        'direct_cause',
        'corrective_actions',
        'preventive_actions',
        'attachments',
        'cost_impact_estimate',
        'downtime_hours_estimate',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'unit_hm_at_failure' => 'decimal:2',
        'component_hm_at_failure' => 'decimal:2',
        'cost_impact_estimate' => 'decimal:2',
        'downtime_hours_estimate' => 'decimal:2',
        'root_cause_5why' => 'array',
        'fishbone_factors' => 'array',
        'attachments' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $far) {
            if (empty($far->far_number)) {
                $monthYear = Carbon::parse($far->incident_date ?? now())->format('ym');
                $prefix = 'FAR-'.$monthYear.'-';
                $last = static::where('far_number', 'LIKE', $prefix.'%')->orderBy('far_number', 'desc')->first();
                $nextSeq = 1;
                if ($last) {
                    $nextSeq = ((int) substr($last->far_number, strlen($prefix))) + 1;
                }
                $far->far_number = $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
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

    public function investigator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'investigator_id');
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function getFailureTypeBadgeAttribute(): array
    {
        return match ($this->failure_type) {
            'premature_failure' => ['label' => 'Premature Failure', 'class' => 'badge-light-warning text-warning'],
            'catastrophic_breakdown' => ['label' => 'Catastrophic Breakdown', 'class' => 'badge-light-danger text-danger'],
            'fatigue_fracture' => ['label' => 'Fatigue Fracture (Patah Lelah)', 'class' => 'badge-light-danger text-danger'],
            'lubrication_failure' => ['label' => 'Lubrication Starvation', 'class' => 'badge-light-warning text-warning'],
            'overheating' => ['label' => 'Thermal Overheating', 'class' => 'badge-light-danger text-danger'],
            'operational_misuse' => ['label' => 'Operational Misuse', 'class' => 'badge-light-info text-info'],
            'assembly_error' => ['label' => 'Assembly / Installation Error', 'class' => 'badge-light-primary text-primary'],
            'wear_out' => ['label' => 'Normal Wear & Tear', 'class' => 'badge-light-success text-success'],
            default => ['label' => ucfirst(str_replace('_', ' ', $this->failure_type)), 'class' => 'badge-light-secondary text-gray-700'],
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'draft' => ['label' => 'Draft', 'class' => 'badge-light-secondary text-gray-600'],
            'under_investigation' => ['label' => 'Dalam Investigasi', 'class' => 'badge-light-warning text-warning'],
            'review_manager' => ['label' => 'Review Plant Manager', 'class' => 'badge-light-info text-info'],
            'closed' => ['label' => 'Selesai (Closed & CAPA Verified)', 'class' => 'badge-light-success text-success'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light-secondary text-gray-600'],
        };
    }
}
