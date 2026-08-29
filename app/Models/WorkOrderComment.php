<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkOrderComment extends BaseModel
{
    protected $table = 'work_order_comments';

    protected $fillable = [
        'work_order_id',
        'parent_id',
        'user_id',
        'body',
        'attachment_path',
        'created_by',
        'updated_by',
    ];

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(WorkOrderComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(WorkOrderComment::class, 'parent_id')->with('user')->orderBy('created_at', 'asc');
    }

    /** Human-readable time ago string. */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /** Avatar initials for the commenter. */
    public function getInitialsAttribute(): string
    {
        $name = $this->user?->full_name ?? 'U';
        $parts = explode(' ', trim($name));
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1).substr($parts[1], 0, 1));
        }

        return strtoupper(substr($name, 0, 2));
    }
}
