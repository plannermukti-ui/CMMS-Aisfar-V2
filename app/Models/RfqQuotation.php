<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfqQuotation extends BaseModel
{
    protected $table = 'rfq_quotations';

    protected $fillable = [
        'rfq_number',
        'purchase_request_id',
        'vendor_id',
        'quotation_number',
        'subtotal_dpp',
        'discount_amount',
        'ppn_percentage',
        'ppn_amount',
        'shipping_cost',
        'grand_total',
        'delivery_lead_time_days',
        'is_selected',
        'notes',
    ];

    protected $casts = [
        'subtotal_dpp' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'ppn_percentage' => 'decimal:2',
        'ppn_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'delivery_lead_time_days' => 'integer',
        'is_selected' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $rfq) {
            if (empty($rfq->rfq_number)) {
                $prefix = 'RFQ-'.date('ym').'-';
                $last = static::where('rfq_number', 'LIKE', $prefix.'%')->orderBy('rfq_number', 'desc')->first();
                $nextSeq = 1;
                if ($last) {
                    $nextSeq = ((int) substr($last->rfq_number, strlen($prefix))) + 1;
                }
                $rfq->rfq_number = $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
