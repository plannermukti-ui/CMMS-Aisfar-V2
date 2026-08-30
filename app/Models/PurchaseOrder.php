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
            $po->items()->each(fn ($i) => $i->delete());
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

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function getItemShippedQuantity(?string $partId = null, ?string $partNumber = null): float
    {
        $doIds = $this->deliveryOrders()->pluck('id');
        if ($doIds->isEmpty()) {
            return 0.0;
        }

        return (float) DeliveryOrderItem::whereIn('delivery_order_id', $doIds)
            ->when($partId, fn ($q) => $q->where('part_id', $partId))
            ->when(! $partId && $partNumber, fn ($q) => $q->where('part_number', $partNumber))
            ->sum('qty_shipped');
    }

    public function getItemReceivedQuantity(?string $partId = null, ?string $partNumber = null): float
    {
        $grIds = $this->goodsReceipts()->pluck('id');
        if ($grIds->isEmpty()) {
            return 0.0;
        }

        return (float) GoodsReceiptItem::whereIn('goods_receipt_id', $grIds)
            ->when($partId, fn ($q) => $q->where('part_id', $partId))
            ->when(! $partId && $partNumber, fn ($q) => $q->where('part_number', $partNumber))
            ->sum('qty_received');
    }

    public function hasUnshippedItems(): bool
    {
        foreach ($this->items as $item) {
            $shipped = $this->getItemShippedQuantity($item->part_id, $item->part_number);
            if ((float) $item->quantity > $shipped) {
                return true;
            }
        }

        return false;
    }

    public function getHasUnshippedItemsAttribute(): bool
    {
        return $this->hasUnshippedItems();
    }

    public function hasUnreceivedItems(): bool
    {
        foreach ($this->items as $item) {
            $received = $this->getItemReceivedQuantity($item->part_id, $item->part_number);
            if ((float) $item->quantity > $received) {
                return true;
            }
        }

        return false;
    }

    public function getHasUnreceivedItemsAttribute(): bool
    {
        return $this->hasUnreceivedItems();
    }

    public function updateCalculatedStatus(): void
    {
        if (in_array($this->status, ['draft', 'cancelled'])) {
            return;
        }

        $totalOrdered = 0;
        $allItemsShipped = true;
        $allItemsReceived = true;
        $anyShipped = false;
        $anyReceived = false;

        foreach ($this->items as $item) {
            $ordered = (float) $item->quantity;
            $shipped = $this->getItemShippedQuantity($item->part_id, $item->part_number);
            $received = $this->getItemReceivedQuantity($item->part_id, $item->part_number);

            $totalOrdered += $ordered;

            if ($shipped < $ordered) {
                $allItemsShipped = false;
            }
            if ($shipped > 0) {
                $anyShipped = true;
            }
            if ($received < $ordered) {
                $allItemsReceived = false;
            }
            if ($received > 0) {
                $anyReceived = true;
            }
        }

        if ($totalOrdered > 0 && $allItemsReceived) {
            $this->update(['status' => 'received']);
        } elseif ($anyReceived) {
            $this->update(['status' => 'partially_received']);
        } elseif ($allItemsShipped && $totalOrdered > 0) {
            $this->update(['status' => 'do_created']);
        } elseif ($anyShipped) {
            $this->update(['status' => 'partially_shipped']);
        } elseif (in_array($this->status, ['partially_shipped', 'do_created', 'partially_received', 'received'])) {
            $this->update(['status' => 'approved']);
        }
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'draft' => ['label' => 'Draft', 'class' => 'badge-light-secondary text-gray-700'],
            'approved' => ['label' => 'Approved (Siap Kirim)', 'class' => 'badge-light-primary text-primary'],
            'sent_to_vendor' => ['label' => 'Sent to Vendor', 'class' => 'badge-light-info text-info'],
            'partially_shipped' => ['label' => 'Sebagian DO (In Transit)', 'class' => 'badge-light-warning text-warning'],
            'do_created' => ['label' => 'Semua DO Terbit (In Transit)', 'class' => 'badge-light-warning text-warning'],
            'partially_received' => ['label' => 'Diterima Sebagian (Partial)', 'class' => 'badge-light-primary text-primary'],
            'received' => ['label' => 'Completed (Received)', 'class' => 'badge-light-success text-success'],
            'cancelled' => ['label' => 'Cancelled', 'class' => 'badge-light-danger text-danger'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light-secondary text-gray-700'],
        };
    }
}
