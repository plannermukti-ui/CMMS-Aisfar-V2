<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryOrderItem extends BaseModel
{
    protected $table = 'delivery_order_items';

    protected $fillable = [
        'delivery_order_id',
        'part_id',
        'part_number',
        'part_name',
        'qty_shipped',
        'uom',
    ];

    protected $casts = [
        'qty_shipped' => 'decimal:2',
    ];

    public function deliveryOrder(): BelongsTo
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }
}
