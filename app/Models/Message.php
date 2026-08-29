<?php

namespace App\Models;

use App\Observers\ActivityObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([ActivityObserver::class])]
class Message extends BaseModel
{
    protected $table = 'messages';

    protected $fillable = [
        'chat_group_id',
        'sender_id',
        'receiver_id',
        'message',
        'attachment_path',
        'attachment_type',
        'attachment_name',
        'attachment_size',
        'read_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'attachment_size' => 'integer',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ChatGroup::class, 'chat_group_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function isImage(): bool
    {
        return $this->attachment_type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->attachment_type === 'video';
    }

    public function isDocument(): bool
    {
        return $this->attachment_type === 'document';
    }

    public function formattedAttachmentSize(): string
    {
        if (! $this->attachment_size) {
            return '';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($this->attachment_size, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2).' '.$units[$pow];
    }
}
