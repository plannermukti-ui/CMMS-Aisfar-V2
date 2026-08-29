<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view message');
    }

    public function view(User $user, Message $message): bool
    {
        return $user->id === $message->sender_id || $user->id === $message->receiver_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create message');
    }

    public function update(User $user, Message $message): bool
    {
        return $user->id === $message->sender_id;
    }

    public function delete(User $user, Message $message): bool
    {
        return $user->id === $message->sender_id || $user->hasPermission('delete message');
    }

    public function restore(User $user, Message $message): bool
    {
        return $user->hasPermission('delete message');
    }

    public function forceDelete(User $user, Message $message): bool
    {
        return $user->hasPermission('delete message');
    }
}
