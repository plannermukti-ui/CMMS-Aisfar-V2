<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PmServiceTypePart extends BaseModel
{
    protected $table = 'pm_service_type_parts';

    protected $fillable = [
        'service_type_task_id',
        'part_number',
        'part_name',
        'quantity',
        'unit',
        'action_type',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function task(): BelongsTo
    {
        return $this->belongsTo(PmServiceTypeTask::class, 'service_type_task_id');
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

    public function getActionTypeBadgeAttribute(): array
    {
        return match ($this->action_type) {
            'replace' => ['label' => 'Ganti Baru', 'class' => 'badge-light-primary text-primary'],
            'check' => ['label' => 'Cek/Kondisi', 'class' => 'badge-light-info text-info'],
            'top_up' => ['label' => 'Tambah/Tambah', 'class' => 'badge-light-warning text-warning'],
            default => ['label' => ucfirst($this->action_type), 'class' => 'badge-light-secondary text-secondary'],
        };
    }
}
