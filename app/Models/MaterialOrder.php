<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialOrder extends BaseModel
{
    protected $table = 'material_orders';

    protected $fillable = [
        'mol_number',
        'mol_date',
        'work_order_id',
        'requester_id',
        'status',
        'approved_by',
        'notes',
    ];

    protected $casts = [
        'mol_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $mol) {
            if (empty($mol->mol_number)) {
                $prefix = 'MOL-'.date('ym').'-';
                $last = static::where('mol_number', 'LIKE', $prefix.'%')->orderBy('mol_number', 'desc')->first();
                $nextSeq = 1;
                if ($last) {
                    $nextSeq = ((int) substr($last->mol_number, strlen($prefix))) + 1;
                }
                $mol->mol_number = $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
            }
            if (empty($mol->mol_date)) {
                $mol->mol_date = now()->toDateString();
            }
        });

        static::deleting(function (self $mol) {
            $mol->items()->each(fn ($i) => $i->delete());
        });
    }

    public function purchaseRequests(): HasMany
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(MaterialOrderItem::class);
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'draft' => ['label' => 'Draft', 'class' => 'badge-light-secondary text-gray-700'],
            'submitted' => ['label' => 'Menunggu Approval', 'class' => 'badge-light-warning text-warning'],
            'approved' => ['label' => 'Approved', 'class' => 'badge-light-primary text-primary'],
            'partially_issued' => ['label' => 'Keluar Sebagian & Sisa PR', 'class' => 'badge-light-info text-info'],
            'issued' => ['label' => 'Issued Penuh (Keluar)', 'class' => 'badge-light-success text-success'],
            'converted_to_pr' => ['label' => 'Diajukan ke PR', 'class' => 'badge-light-primary text-primary'],
            'rejected' => ['label' => 'Ditolak', 'class' => 'badge-light-danger text-danger'],
            default => ['label' => ucfirst(str_replace('_', ' ', $this->status)), 'class' => 'badge-light-secondary text-gray-700'],
        };
    }
}
