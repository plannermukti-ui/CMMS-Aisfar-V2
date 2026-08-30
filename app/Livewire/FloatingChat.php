<?php

namespace App\Livewire;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class FloatingChat extends Component
{
    public $isOpen = false;

    public $selectedUser = null;

    public $messageText = '';

    public $users = [];

    public $messages = [];

    public function mount()
    {
        $this->loadUsers();
    }

    public function loadUsers()
    {
        if (Auth::check()) {
            $this->users = User::with('position')
                ->where('id', '!=', Auth::id())
                ->orderBy('full_name')
                ->get();
        }
    }

    public function selectUser($userId)
    {
        $this->selectedUser = User::find($userId);
        $this->loadMessages();
    }

    public function goBack()
    {
        $this->selectedUser = null;
        $this->messages = [];
    }

    public function toggleChat()
    {
        $this->isOpen = ! $this->isOpen;
        if ($this->isOpen && ! $this->users) {
            $this->loadUsers();
        }
    }

    public function loadMessages()
    {
        if (! $this->selectedUser) {
            return;
        }

        $authId = Auth::id();
        $this->messages = Message::where(function ($q) use ($authId) {
            $q->where('sender_id', $authId)->where('receiver_id', $this->selectedUser->id);
        })
            ->orWhere(function ($q) use ($authId) {
                $q->where('sender_id', $this->selectedUser->id)->where('receiver_id', $authId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark as read
        Message::where('sender_id', $this->selectedUser->id)
            ->where('receiver_id', $authId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->dispatch('scroll-to-bottom');
    }

    public function sendMessage()
    {
        $text = trim($this->messageText);
        if (empty($text) || ! $this->selectedUser) {
            return;
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedUser->id,
            'message' => $text,
        ]);

        $this->messageText = '';

        try {
            broadcast(new MessageSent($message));
        } catch (\Throwable $e) {
            Log::warning('Chat broadcast notice: '.$e->getMessage());
        }

        $this->loadMessages();
    }

    public function getListeners()
    {
        return [
            'echo-private:chat.'.Auth::id().',MessageSent' => 'onMessageReceived',
        ];
    }

    public function onMessageReceived($event)
    {
        // If chat is open with the sender, reload messages
        if ($this->selectedUser && $this->selectedUser->id === $event['message']['sender_id']) {
            $this->loadMessages();
            $this->dispatch('scroll-to-bottom');
        } else {
            // Otherwise, we could show a badge or notification
            $this->dispatch('new-message-received');
        }
    }

    public function render()
    {
        return view('livewire.floating-chat');
    }
}
