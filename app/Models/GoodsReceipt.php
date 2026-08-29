<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoodsReceipt extends BaseModel
{
    protected $table = 'goods_receipts';

    protected $fillable = [
        'gr_number',
        'gr_date',
        'purchase_order_id',
        'delivery_order_id',
        'site_id',
        'delivery_order_number',
        'received_by_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'gr_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $gr) {
            if (empty($gr->gr_number)) {
                $prefix = 'GR-'.date('ym').'-';
                $last = static::where('gr_number', 'LIKE', $prefix.'%')->orderBy('gr_number', 'desc')->first();
                $nextSeq = 1;
                if ($last) {
                    $nextSeq = ((int) substr($last->gr_number, strlen($prefix))) + 1;
                }
                $gr->gr_number = $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
            }
            if (empty($gr->gr_date)) {
                $gr->gr_date = now()->toDateString();
            }
        });

        static::deleting(function (self $gr) {
            $gr->items()->each(fn ($i) => $i->delete());
        });
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function deliveryOrder(): BelongsTo
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'completed' => ['label' => 'Diterima & Stok Bertambah', 'class' => 'badge-light-success text-success'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light-secondary text-gray-700'],
        };
    }
}
