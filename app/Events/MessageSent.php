<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        if ($this->message->chat_group_id) {
            return [
                new PrivateChannel('chat.group.'.$this->message->chat_group_id),
            ];
        }

        return [
            new PrivateChannel('chat.'.$this->message->receiver_id),
            new PrivateChannel('chat.'.$this->message->sender_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->full_name ?? $this->message->sender->name ?? 'User',
            'receiver_id' => $this->message->receiver_id,
            'chat_group_id' => $this->message->chat_group_id,
            'message' => $this->message->message,
            'attachment_path' => $this->message->attachment_path ? asset('storage/'.$this->message->attachment_path) : null,
            'attachment_type' => $this->message->attachment_type,
            'attachment_name' => $this->message->attachment_name,
            'attachment_size' => $this->message->formattedAttachmentSize(),
            'created_at' => $this->message->created_at->toIso8601String(),
        ];
    }
}
