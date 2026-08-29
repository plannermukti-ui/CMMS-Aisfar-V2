<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderSubtaskSparepart extends BaseModel
{
    protected $table = 'work_order_subtask_spareparts';

    protected $fillable = [
        'work_order_subtask_id',
        'part_id',
        'part_number',
        'part_name',
        'quantity',
        'unit',
        'action_type',
        'status',
        'source_unit',
        'remarks',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function subtask(): BelongsTo
    {
        return $this->belongsTo(WorkOrderSubtask::class, 'work_order_subtask_id');
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class, 'part_id');
    }

    public function getActionTypeBadgeAttribute(): array
    {
        return match ($this->action_type) {
            'replace' => ['label' => 'Ganti Baru', 'class' => 'badge-light-primary text-primary'],
            'swap' => ['label' => 'Swap / Kanibal', 'class' => 'badge-light-warning text-warning'],
            'repair' => ['label' => 'Perbaiki Part', 'class' => 'badge-light-info text-info'],
            default => ['label' => ucfirst($this->action_type), 'class' => 'badge-light-secondary text-gray-700'],
        };
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'installed' => ['label' => 'Terpasang', 'class' => 'badge-light-success text-success'],
            'waiting_part' => ['label' => 'Waiting Part', 'class' => 'badge-light-danger text-danger'],
            'cancelled' => ['label' => 'Batal', 'class' => 'badge-light-secondary text-gray-700'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light-secondary text-gray-700'],
        };
    }
}
