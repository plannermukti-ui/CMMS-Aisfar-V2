<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends BaseModel
{
    protected $table = 'purchase_orders';

    protected $fillable = [
        'po_number',
        'po_date',
        'purchase_request_id',
        'rfq_quotation_id',
        'vendor_id',
        'delivery_target_date',
        'subtotal_dpp',
        'discount_amount',
        'ppn_percentage',
        'ppn_amount',
        'shipping_cost',
        'grand_total',
        'payment_terms',
        'status',
        'approved_by',
        'notes',
    ];

    protected $casts = [
        'po_date' => 'date',
        'delivery_target_date' => 'date',
        'subtotal_dpp' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'ppn_percentage' => 'decimal:2',
        'ppn_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $po) {
            if (empty($po->po_number)) {
                $prefix = 'PO-'.date('ym').'-';
                $last = static::where('po_number', 'LIKE', $prefix.'%')->orderBy('po_number', 'desc')->first();
                $nextSeq = 1;
                if ($last) {
                    $nextSeq = ((int) substr($last->po_number, strlen($prefix))) + 1;
                }
                $po->po_number = $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
            }
            if (empty($po->po_date)) {
                $po->po_date = now()->toDateString();
            }
        });

        static::deleting(function (self $po) {
            $po->deliveryOrders()->each(fn ($d) => $d->delete());
        });
    }

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function rfqQuotation(): BelongsTo
    {
        return $this->belongsTo(RfqQuotation::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'draft' => ['label' => 'Draft', 'class' => 'badge-light-secondary text-gray-700'],
            'approved' => ['label' => 'Approved (Siap Kirim)', 'class' => 'badge-light-primary text-primary'],
            'sent_to_vendor' => ['label' => 'Sent to Vendor', 'class' => 'badge-light-info text-info'],
            'do_created' => ['label' => 'DO Created (In Transit)', 'class' => 'badge-light-warning text-warning'],
            'received' => ['label' => 'Completed (Received)', 'class' => 'badge-light-success text-success'],
            'cancelled' => ['label' => 'Cancelled', 'class' => 'badge-light-danger text-danger'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light-secondary text-gray-700'],
        };
    }
}
