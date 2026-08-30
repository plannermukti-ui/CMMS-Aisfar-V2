<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfqQuotationItem extends BaseModel
{
    protected $table = 'rfq_quotation_items';

    protected $fillable = [
        'rfq_quotation_id',
        'purchase_request_item_id',
        'status',
        'qty_ready',
        'unit_price',
        'discount_amount',
        'subtotal',
        'is_selected',
    ];

    protected $casts = [
        'qty_ready' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'is_selected' => 'boolean',
    ];

    public function rfqQuotation(): BelongsTo
    {
        return $this->belongsTo(RfqQuotation::class);
    }

    public function purchaseRequestItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequestItem::class);
    }
}
