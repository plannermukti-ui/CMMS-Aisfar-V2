<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryOrder extends BaseModel
{
    protected $table = 'delivery_orders';

    protected $fillable = [
        'do_number',
        'do_date',
        'purchase_order_id',
        'origin_location',
        'destination_site_id',
        'destination_location_name',
        'expedition_name',
        'vehicle_plate_number',
        'tracking_number',
        'departure_date',
        'estimated_arrival_date',
        'actual_arrival_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'do_date' => 'date',
        'departure_date' => 'datetime',
        'estimated_arrival_date' => 'datetime',
        'actual_arrival_date' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $do) {
            if (empty($do->do_number)) {
                $prefix = 'DO-'.date('ym').'-';
                $last = static::where('do_number', 'LIKE', $prefix.'%')->orderBy('do_number', 'desc')->first();
                $nextSeq = 1;
                if ($last) {
                    $nextSeq = ((int) substr($last->do_number, strlen($prefix))) + 1;
                }
                $do->do_number = $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
            }
            if (empty($do->do_date)) {
                $do->do_date = now()->toDateString();
            }
        });

        static::deleting(function (self $do) {
            $do->items()->each(fn ($i) => $i->delete());
        });
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function destinationSite(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'destination_site_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DeliveryOrderItem::class);
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
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

    public function hasUnreceivedItems(): bool
    {
        foreach ($this->items as $item) {
            $received = $this->getItemReceivedQuantity($item->part_id, $item->part_number);
            if ((float) $item->qty_shipped > $received) {
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
        $totalShipped = 0;
        $allItemsReceived = true;
        $anyReceived = false;

        foreach ($this->items as $item) {
            $shipped = (float) $item->qty_shipped;
            $received = $this->getItemReceivedQuantity($item->part_id, $item->part_number);

            $totalShipped += $shipped;

            if ($received < $shipped) {
                $allItemsReceived = false;
            }
            if ($received > 0) {
                $anyReceived = true;
            }
        }

        if ($totalShipped > 0 && $allItemsReceived) {
            $this->update([
                'status' => 'received',
                'actual_arrival_date' => $this->actual_arrival_date ?: now(),
            ]);
        } elseif ($anyReceived) {
            $this->update([
                'status' => 'partially_received',
                'actual_arrival_date' => $this->actual_arrival_date ?: now(),
            ]);
        }
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'in_transit' => ['label' => 'Dalam Pengiriman (In Transit)', 'class' => 'badge-light-warning text-warning'],
            'arrived' => ['label' => 'Tiba di Lokasi / Site', 'class' => 'badge-light-info text-info'],
            'partially_received' => ['label' => 'Diterima Sebagian', 'class' => 'badge-light-primary text-primary'],
            'received' => ['label' => 'Diterima Lengkap & Di-GR', 'class' => 'badge-light-success text-success'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light-secondary text-gray-700'],
        };
    }
}
