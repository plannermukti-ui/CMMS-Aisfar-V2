<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequestItem extends BaseModel
{
    protected $table = 'purchase_request_items';

    protected $fillable = [
        'purchase_request_id',
        'part_id',
        'part_number',
        'part_name',
        'quantity',
        'uom',
        'estimated_unit_price',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'estimated_unit_price' => 'decimal:2',
    ];

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }
}
