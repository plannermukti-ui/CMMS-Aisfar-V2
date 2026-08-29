<?php

namespace App\Livewire\User;

use App\Events\MessageSent;
use App\Models\ChatGroup;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.user')]
#[Title('Chat & Messenger')]
class ChatPage extends Component
{
    use WithFileUploads;

    public string $activeType = 'direct'; // 'direct' or 'group'

    public ?string $activeId = null;

    public $activeTarget = null; // User or ChatGroup instance

    public string $messageText = '';

    public $attachment = null;

    public string $searchQuery = '';

    public string $tab = 'all'; // 'all', 'direct', 'groups'

    // Group Creation State
    public bool $showNewGroupModal = false;

    public bool $showGroupInfoModal = false;

    public string $newGroupName = '';

    public string $newGroupDescription = '';

    public array $newGroupMembers = [];

    // Emoji Picker state
    public bool $showEmojiPicker = false;

    public function mount(?string $user = null, ?string $group = null): void
    {
        if ($group) {
            $this->selectConversation('group', $group);
        } elseif ($user) {
            $this->selectConversation('direct', $user);
        } else {
            // Auto select the most recent conversation if available
            $this->selectDefaultConversation();
        }
    }

    public function selectDefaultConversation(): void
    {
        $authId = Auth::id();

        // Check latest message
        $latestMsg = Message::where('sender_id', $authId)
            ->orWhere('receiver_id', $authId)
            ->orWhereIn('chat_group_id', function ($q) use ($authId) {
                $q->select('chat_group_id')
                    ->from('chat_group_users')
                    ->where('user_id', $authId);
            })
            ->latest('created_at')
            ->first();

        if ($latestMsg) {
            if ($latestMsg->chat_group_id) {
                $this->selectConversation('group', $latestMsg->chat_group_id);
            } else {
                $targetId = ($latestMsg->sender_id === $authId) ? $latestMsg->receiver_id : $latestMsg->sender_id;
                if ($targetId) {
                    $this->selectConversation('direct', $targetId);
                }
            }
        } else {
            // Pick first user if exists
            $firstUser = User::where('id', '!=', $authId)->first();
            if ($firstUser) {
                $this->selectConversation('direct', $firstUser->id);
            }
        }
    }

    public function selectConversation(string $type, string $id): void
    {
        $this->activeType = $type;
        $this->activeId = $id;
        $this->attachment = null;
        $this->messageText = '';
        $this->showEmojiPicker = false;

        if ($type === 'group') {
            $this->activeTarget = ChatGroup::with(['users', 'creator'])->find($id);
        } else {
            $this->activeTarget = User::with(['department', 'position'])->find($id);

            // Mark as read
            if ($this->activeTarget) {
                Message::where('sender_id', $id)
                    ->where('receiver_id', Auth::id())
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            }
        }

        $this->dispatch('scroll-to-bottom');
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function toggleEmojiPicker(): void
    {
        $this->showEmojiPicker = ! $this->showEmojiPicker;
    }

    public function addEmoji(string $emoji): void
    {
        $this->messageText .= $emoji;
        $this->showEmojiPicker = false;
    }

    public function removeAttachment(): void
    {
        $this->attachment = null;
    }

    public function sendMessage(): void
    {
        $text = trim($this->messageText);

        if (empty($text) && ! $this->attachment) {
            return;
        }

        if (! $this->activeId) {
            return;
        }

        $attachmentPath = null;
        $attachmentType = null;
        $attachmentName = null;
        $attachmentSize = null;

        if ($this->attachment) {
            $this->validate([
                'attachment' => 'file|max:51200', // 50MB max
            ]);

            $originalName = $this->attachment->getClientOriginalName();
            $mime = $this->attachment->getMimeType();
            $attachmentSize = $this->attachment->getSize();
            $attachmentName = $originalName;

            // Determine type
            if (str_starts_with($mime, 'image/')) {
                $attachmentType = 'image';
            } elseif (str_starts_with($mime, 'video/')) {
                $attachmentType = 'video';
            } elseif (str_starts_with($mime, 'audio/')) {
                $attachmentType = 'audio';
            } else {
                $attachmentType = 'document';
            }

            $attachmentPath = $this->attachment->store('chat-attachments', 'public');
        }

        $authId = Auth::id();

        $messageData = [
            'sender_id' => $authId,
            'message' => $text ?? '',
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'attachment_name' => $attachmentName,
            'attachment_size' => $attachmentSize,
        ];

        if ($this->activeType === 'group') {
            $messageData['chat_group_id'] = $this->activeId;
            $messageData['receiver_id'] = null;
        } else {
            $messageData['chat_group_id'] = null;
            $messageData['receiver_id'] = $this->activeId;
        }

        $message = Message::create($messageData);

        // Reset input fields
        $this->messageText = '';
        $this->attachment = null;
        $this->showEmojiPicker = false;

        // Broadcast to channels
        try {
            broadcast(new MessageSent($message));
        } catch (\Throwable $e) {
            Log::warning('Chat broadcast notice: '.$e->getMessage());
        }

        $this->dispatch('scroll-to-bottom');
    }

    public function openNewGroupModal(): void
    {
        $this->newGroupName = '';
        $this->newGroupDescription = '';
        $this->newGroupMembers = [];
        $this->showNewGroupModal = true;
    }

    public function createGroup(): void
    {
        $this->validate([
            'newGroupName' => 'required|min:2|max:100',
            'newGroupMembers' => 'required|array|min:1',
        ], [
            'newGroupName.required' => 'Nama grup wajib diisi.',
            'newGroupMembers.required' => 'Pilih minimal satu anggota grup.',
        ]);

        $group = ChatGroup::create([
            'name' => $this->newGroupName,
            'description' => $this->newGroupDescription,
            'created_by' => Auth::id(),
        ]);

        // Attach creator as admin
        $group->users()->attach(Auth::id(), ['role' => 'admin']);

        // Attach selected members
        foreach ($this->newGroupMembers as $memberId) {
            if ($memberId !== Auth::id()) {
                $group->users()->attach($memberId, ['role' => 'member']);
            }
        }

        $this->showNewGroupModal = false;

        // Auto select new group
        $this->selectConversation('group', $group->id);
    }

    public function openGroupInfo(): void
    {
        if ($this->activeType === 'group') {
            $this->showGroupInfoModal = true;
        }
    }

    public function getConversationsProperty(): array
    {
        $authId = Auth::id();
        $query = strtolower(trim($this->searchQuery));
        $results = [];

        // 1. Direct Users
        if ($this->tab === 'all' || $this->tab === 'direct') {
            $users = User::where('id', '!=', $authId)
                ->with(['department', 'position'])
                ->when($query, function ($q) use ($query) {
                    $q->where(function ($sub) use ($query) {
                        $sub->whereRaw('LOWER(full_name) LIKE ?', ['%'.$query.'%'])
                            ->orWhereRaw('LOWER(email) LIKE ?', ['%'.$query.'%']);
                    });
                })
                ->get();

            foreach ($users as $user) {
                // Find latest message with this user
                $latest = Message::where(function ($q) use ($authId, $user) {
                    $q->where('sender_id', $authId)->where('receiver_id', $user->id);
                })->orWhere(function ($q) use ($authId, $user) {
                    $q->where('sender_id', $user->id)->where('receiver_id', $authId);
                })->latest('created_at')->first();

                // Count unread
                $unread = Message::where('sender_id', $user->id)
                    ->where('receiver_id', $authId)
                    ->whereNull('read_at')
                    ->count();

                $results[] = [
                    'type' => 'direct',
                    'id' => $user->id,
                    'name' => $user->full_name ?? $user->name ?? 'User',
                    'subtitle' => $user->position->name ?? $user->department->name ?? $user->email,
                    'avatar' => $user->photo,
                    'initial' => strtoupper(substr($user->full_name ?? $user->name ?? 'U', 0, 1)),
                    'unread' => $unread,
                    'latest_message' => $latest ? ($latest->attachment_type ? '📎 ['.ucfirst($latest->attachment_type).']' : $latest->message) : null,
                    'latest_time' => $latest ? $latest->created_at : null,
                    'is_online' => true,
                ];
            }
        }

        // 2. Chat Groups
        if ($this->tab === 'all' || $this->tab === 'groups') {
            $groups = Auth::user()->chatGroups()
                ->with(['users'])
                ->when($query, function ($q) use ($query) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%'.$query.'%']);
                })
                ->get();

            foreach ($groups as $group) {
                $latest = Message::where('chat_group_id', $group->id)->latest('created_at')->first();

                $results[] = [
                    'type' => 'group',
                    'id' => $group->id,
                    'name' => $group->name,
                    'subtitle' => $group->users->count().' anggota',
                    'avatar' => $group->avatar,
                    'initial' => strtoupper(substr($group->name, 0, 1)),
                    'unread' => 0,
                    'latest_message' => $latest ? ($latest->attachment_type ? '📎 ['.ucfirst($latest->attachment_type).']' : $latest->message) : null,
                    'latest_time' => $latest ? $latest->created_at : null,
                    'is_online' => null,
                ];
            }
        }

        // Sort by latest_time descending
        usort($results, function ($a, $b) {
            if (! $a['latest_time']) {
                return 1;
            }
            if (! $b['latest_time']) {
                return -1;
            }

            return $b['latest_time']->timestamp <=> $a['latest_time']->timestamp;
        });

        return $results;
    }

    public function getActiveMessagesProperty()
    {
        if (! $this->activeId) {
            return collect();
        }

        $authId = Auth::id();

        if ($this->activeType === 'group') {
            return Message::where('chat_group_id', $this->activeId)
                ->with('sender')
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return Message::where(function ($q) use ($authId) {
            $q->where('sender_id', $authId)->where('receiver_id', $this->activeId);
        })->orWhere(function ($q) use ($authId) {
            $q->where('sender_id', $this->activeId)->where('receiver_id', $authId);
        })
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function getAvailableUsersProperty()
    {
        return User::where('id', '!=', Auth::id())
            ->orderBy('full_name')
            ->get();
    }

    public function render()
    {
        return view('livewire.user.chat-page', [
            'conversations' => $this->conversations,
            'messages' => $this->activeMessages,
            'availableUsers' => $this->availableUsers,
        ]);
    }
}
