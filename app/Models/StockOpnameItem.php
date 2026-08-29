<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends BaseModel
{
    protected $table = 'stock_opname_items';

    protected $fillable = [
        'stock_opname_id',
        'part_id',
        'part_number',
        'part_name',
        'uom',
        'rack_location',
        'system_stock',
        'physical_stock',
        'difference_qty',
        'unit_cost',
        'variance_cost',
        'discrepancy_notes',
    ];

    protected $casts = [
        'system_stock' => 'decimal:2',
        'physical_stock' => 'decimal:2',
        'difference_qty' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'variance_cost' => 'decimal:2',
    ];

    public function stockOpname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }
}
