<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Part extends BaseModel
{
    protected $table = 'parts';

    protected $fillable = [
        'part_number',
        'name',
        'description',
        'vendor_id',
        'category',
        'uom',
        'stock_on_hand',
        'min_stock',
        'max_stock',
        'bin_location',
        'standard_cost',
        'is_active',
    ];

    protected $casts = [
        'stock_on_hand' => 'decimal:2',
        'min_stock' => 'decimal:2',
        'max_stock' => 'decimal:2',
        'standard_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function locations(): HasMany
    {
        return $this->hasMany(PartLocation::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function getMinimumStockAttribute(): float
    {
        return (float) $this->min_stock;
    }

    public function isStockLow(): bool
    {
        return $this->stock_on_hand <= $this->min_stock;
    }

    public function getStockBadgeAttribute(): array
    {
        if ($this->stock_on_hand <= 0) {
            return ['label' => 'Habis (0)', 'class' => 'badge-light-danger text-danger'];
        }
        if ($this->stock_on_hand <= $this->min_stock) {
            return ['label' => 'Stok Kritis ('.$this->stock_on_hand.' '.$this->uom.')', 'class' => 'badge-light-warning text-warning'];
        }

        return ['label' => 'Aman ('.$this->stock_on_hand.' '.$this->uom.')', 'class' => 'badge-light-success text-success'];
    }
}
