<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialOrderItem extends BaseModel
{
    protected $table = 'material_order_items';

    protected $fillable = [
        'material_order_id',
        'part_id',
        'part_number',
        'part_name',
        'qty_requested',
        'qty_issued',
        'status',
    ];

    protected $casts = [
        'qty_requested' => 'decimal:2',
        'qty_issued' => 'decimal:2',
    ];

    public function materialOrder(): BelongsTo
    {
        return $this->belongsTo(MaterialOrder::class);
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }

    public function getShortageQtyAttribute(): float
    {
        return max(0, (float) $this->qty_requested - (float) $this->qty_issued);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'ready_stock' => ['label' => 'Stok Cukup', 'class' => 'badge-light-success text-success'],
            'out_of_stock' => ['label' => 'Stok Kurang / Kosong', 'class' => 'badge-light-danger text-danger'],
            'partially_issued' => ['label' => 'Keluar Sebagian ('.$this->qty_issued.'/'.$this->qty_requested.')', 'class' => 'badge-light-info text-info'],
            'issued' => ['label' => 'Keluar Penuh ('.$this->qty_issued.')', 'class' => 'badge-light-success text-success'],
            'pr_generated' => ['label' => 'PR Dibuat', 'class' => 'badge-light-primary text-primary'],
            default => ['label' => ucfirst(str_replace('_', ' ', $this->status)), 'class' => 'badge-light-secondary text-gray-700'],
        };
    }
}
