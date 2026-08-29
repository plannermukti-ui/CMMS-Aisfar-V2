<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceiptItem extends BaseModel
{
    protected $table = 'goods_receipt_items';

    protected $fillable = [
        'goods_receipt_id',
        'part_id',
        'part_number',
        'part_name',
        'qty_received',
        'unit_price',
    ];

    protected $casts = [
        'qty_received' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }
}
