<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequest extends BaseModel
{
    protected $table = 'purchase_requests';

    protected $fillable = [
        'pr_number',
        'pr_date',
        'material_order_id',
        'requester_id',
        'priority',
        'required_date',
        'status',
        'approved_by',
        'remarks',
    ];

    protected $casts = [
        'pr_date' => 'date',
        'required_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $pr) {
            if (empty($pr->pr_number)) {
                $prefix = 'PR-'.date('ym').'-';
                $last = static::where('pr_number', 'LIKE', $prefix.'%')->orderBy('pr_number', 'desc')->first();
                $nextSeq = 1;
                if ($last) {
                    $nextSeq = ((int) substr($last->pr_number, strlen($prefix))) + 1;
                }
                $pr->pr_number = $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
            }
            if (empty($pr->pr_date)) {
                $pr->pr_date = now()->toDateString();
            }
        });

        static::deleting(function (self $pr) {
            $pr->items()->each(fn ($i) => $i->delete());
            $pr->quotations()->each(fn ($q) => $q->delete());
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(RfqQuotation::class);
    }

    public function materialOrder(): BelongsTo
    {
        return $this->belongsTo(MaterialOrder::class);
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
            'approved' => ['label' => 'Approved (Siap RFQ)', 'class' => 'badge-light-primary text-primary'],
            'rfq_created' => ['label' => 'RFQ In Process', 'class' => 'badge-light-info text-info'],
            'po_created' => ['label' => 'PO Issued', 'class' => 'badge-light-success text-success'],
            'rejected' => ['label' => 'Ditolak', 'class' => 'badge-light-danger text-danger'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light-secondary text-gray-700'],
        };
    }
}
