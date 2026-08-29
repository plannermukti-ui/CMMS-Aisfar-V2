<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlantOsr extends BaseModel
{
    protected $table = 'plant_outside_repairs';

    protected $fillable = [
        'osr_number',
        'order_date',
        'equipment_id',
        'component_id',
        'work_order_id',
        'vendor_id',
        'item_description',
        'scope_of_work',
        'reason_for_outside',
        'dispatch_date',
        'estimated_completion_date',
        'actual_completion_date',
        'delivery_letter_number',
        'received_letter_number',
        'estimated_cost',
        'actual_cost',
        'warranty_period_months',
        'warranty_period_hours',
        'status',
        'qc_passed',
        'qc_notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'dispatch_date' => 'date',
        'estimated_completion_date' => 'date',
        'actual_completion_date' => 'date',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'warranty_period_months' => 'integer',
        'warranty_period_hours' => 'integer',
        'qc_passed' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $osr) {
            if (empty($osr->osr_number)) {
                $monthYear = Carbon::parse($osr->order_date ?? now())->format('ym');
                $prefix = 'OSR-'.$monthYear.'-';
                $last = static::where('osr_number', 'LIKE', $prefix.'%')->orderBy('osr_number', 'desc')->first();
                $nextSeq = 1;
                if ($last) {
                    $nextSeq = ((int) substr($last->osr_number, strlen($prefix))) + 1;
                }
                $osr->osr_number = $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
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

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class, 'work_order_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'draft' => ['label' => 'Draft Order', 'class' => 'badge-light-secondary text-gray-600'],
            'dispatched' => ['label' => 'Terkirim ke Vendor', 'class' => 'badge-light-primary text-primary'],
            'vendor_inspecting' => ['label' => 'Inspeksi & Estimasi Vendor', 'class' => 'badge-light-warning text-warning'],
            'quotation_approved' => ['label' => 'Quotation Disetujui', 'class' => 'badge-light-info text-info'],
            'in_progress' => ['label' => 'Pengerjaan / Machining', 'class' => 'badge-light-warning text-warning'],
            'testing_qc' => ['label' => 'Uji Test Bench / QC', 'class' => 'badge-light-primary text-primary'],
            'received_at_site' => ['label' => 'Tiba di Site (Ready Pasang)', 'class' => 'badge-light-success text-success'],
            'closed' => ['label' => 'Selesai & Bergaransi', 'class' => 'badge-light-success text-success'],
            'rejected_warranty' => ['label' => 'Klaim Garansi / Reject', 'class' => 'badge-light-danger text-danger'],
            default => ['label' => ucfirst(str_replace('_', ' ', $this->status)), 'class' => 'badge-light-secondary text-gray-700'],
        };
    }
}
