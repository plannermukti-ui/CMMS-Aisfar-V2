<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PmServiceTypeTask extends BaseModel
{
    protected $table = 'pm_service_type_tasks';

    protected $fillable = [
        'service_type_id',
        'task_title',
        'task_order',
        'notes',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'task_order' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(PmServiceType::class, 'service_type_id');
    }

    public function parts(): HasMany
    {
        return $this->hasMany(PmServiceTypePart::class, 'service_type_task_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
