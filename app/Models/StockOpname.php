<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends BaseModel
{
    protected $table = 'stock_opnames';

    protected $fillable = [
        'opname_number',
        'opname_date',
        'site_id',
        'conducted_by_id',
        'status',
        'approved_by_id',
        'notes',
        'discrepancy_reason',
        'berita_acara_number',
        'total_system_items',
        'total_variance_qty',
        'total_variance_value',
    ];

    protected $casts = [
        'opname_date' => 'date',
        'total_system_items' => 'integer',
        'total_variance_qty' => 'decimal:2',
        'total_variance_value' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $so) {
            if (empty($so->opname_number)) {
                $prefix = 'SO-'.date('ym').'-';
                $last = static::where('opname_number', 'LIKE', $prefix.'%')->orderBy('opname_number', 'desc')->first();
                $nextSeq = 1;
                if ($last) {
                    $nextSeq = ((int) substr($last->opname_number, strlen($prefix))) + 1;
                }
                $so->opname_number = $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
            }
            if (empty($so->opname_date)) {
                $so->opname_date = now()->toDateString();
            }
            if (empty($so->berita_acara_number)) {
                $baPrefix = 'BA-SO-'.date('ym').'-';
                $lastBa = static::where('berita_acara_number', 'LIKE', $baPrefix.'%')->orderBy('berita_acara_number', 'desc')->first();
                $nextBaSeq = 1;
                if ($lastBa) {
                    $nextBaSeq = ((int) substr($lastBa->berita_acara_number, strlen($baPrefix))) + 1;
                }
                $so->berita_acara_number = $baPrefix.str_pad((string) $nextBaSeq, 4, '0', STR_PAD_LEFT);
            }
        });

        static::deleting(function (self $so) {
            $so->items()->each(fn ($i) => $i->delete());
        });
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function conductedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conducted_by_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'draft' => ['label' => 'Draft', 'class' => 'badge-light-secondary text-gray-700'],
            'submitted' => ['label' => 'Menunggu Approval', 'class' => 'badge-light-warning text-warning'],
            'approved' => ['label' => 'Approved & Stok Disesuaikan', 'class' => 'badge-light-success text-success'],
            'rejected' => ['label' => 'Ditolak', 'class' => 'badge-light-danger text-danger'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light-secondary text-gray-700'],
        };
    }
}
