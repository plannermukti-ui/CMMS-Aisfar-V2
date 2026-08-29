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

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'in_transit' => ['label' => 'Dalam Pengiriman (In Transit)', 'class' => 'badge-light-warning text-warning'],
            'arrived' => ['label' => 'Tiba di Lokasi / Site', 'class' => 'badge-light-info text-info'],
            'received' => ['label' => 'Diterima & Di-GR', 'class' => 'badge-light-success text-success'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light-secondary text-gray-700'],
        };
    }
}
