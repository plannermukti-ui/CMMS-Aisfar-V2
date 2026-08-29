<div
    x-data="{
        showEmojiPicker: @entangle('showEmojiPicker'),
        emojis: [
            '😀', '😃', '😄', '😁', '😆', '😅', '😂', '🤣', '😊', '😇',
            '🙂', '🙃', '😉', '😌', '😍', '🥰', '😘', '😗', '😙', '😚',
            '😋', '😛', '😝', '😜', '🤪', '🤨', '🧐', '🤓', '😎', '🤩',
            '🥳', '😏', '😒', '😞', '😔', '😟', '😕', '🙁', '☹️', '😣',
            '😖', '😫', '😩', '🥺', '😢', '😭', '😤', '😠', '😡', '🤬',
            '👍', '👎', '👌', '✌️', '🤞', '🤟', '🤘', '🤙', '👈', '👉',
            '👆', '👇', '☝️', '✋', '🤚', '🖐', '🖖', '👋', '🤝', '💪',
            '❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔',
            '🔥', '✨', '🎉', '🎊', '🚀', '⭐', '🌟', '💡', '📌', '📎',
            '✅', '❌', '⚠️', '💯', '🏆', '🎯', '📦', '📊', '📈', '📋'
        ],
        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                this.$nextTick(() => {
                    container.scrollTop = container.scrollHeight;
                });
            }
        }
    }"
    @scroll-to-bottom.window="scrollToBottom()"
    wire:poll.4s
    class="d-flex flex-column flex-lg-row gap-5"
>
    <!--begin::Sidebar (Conversation List)-->
    <div class="w-100 w-lg-350px w-xl-400px flex-shrink-0">
        <div class="card card-flush shadow-sm border border-gray-200 h-lg-100">
            <!--begin::Card header-->
            <div class="card-header pt-6 pb-4">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-40px me-3">
                            <div class="symbol-label bg-light-primary text-primary fw-bold">
                                <i class="ki-outline ki-messages fs-2 text-primary"></i>
                            </div>
                        </div>
                        <div>
                            <h3 class="fs-4 fw-bold text-gray-900 mb-0">Chat & Messenger</h3>
                            <span class="fs-7 text-muted">Kolaborasi Tim Real-Time</span>
                        </div>
                    </div>
                    
                    <!-- Create Group Button -->
                    <button
                        type="button"
                        wire:click="openNewGroupModal"
                        class="btn btn-sm btn-icon btn-light-primary"
                        title="Buat Grup Baru"
                        data-bs-toggle="tooltip"
                    >
                        <i class="ki-outline ki-user-edit fs-2"></i>
                    </button>
                </div>

                <!-- Search Form -->
                <div class="w-100 position-relative mt-4">
                    <i class="ki-outline ki-magnifier fs-3 position-absolute ms-4 translate-middle-y top-50 text-gray-500"></i>
                    <input
                        type="text"
                        wire:model.live.debounce.250ms="searchQuery"
                        class="form-control form-control-solid ps-12 fs-7"
                        placeholder="Cari percakapan, orang, grup..."
                    />
                </div>

                <!-- Tabs Filter (Semua, Direct, Grup) -->
                <div class="d-flex align-items-center gap-2 mt-3 w-100">
                    <button
                        type="button"
                        wire:click="setTab('all')"
                        class="btn btn-sm py-1 px-3 fs-8 fw-bold {{ $tab === 'all' ? 'btn-primary' : 'btn-light text-muted' }}"
                    >
                        Semua
                    </button>
                    <button
                        type="button"
                        wire:click="setTab('direct')"
                        class="btn btn-sm py-1 px-3 fs-8 fw-bold {{ $tab === 'direct' ? 'btn-primary' : 'btn-light text-muted' }}"
                    >
                        Direct (1-on-1)
                    </button>
                    <button
                        type="button"
                        wire:click="setTab('groups')"
                        class="btn btn-sm py-1 px-3 fs-8 fw-bold {{ $tab === 'groups' ? 'btn-primary' : 'btn-light text-muted' }}"
                    >
                        Grup
                    </button>
                </div>
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body p-0">
                <!--begin::List-->
                <div class="hover-scroll-overlay-y px-4 py-2" style="max-height: calc(100vh - 340px); min-height: 480px;">
                    @forelse($conversations as $conv)
                        @php
                            $isActive = ($activeType === $conv['type'] && $activeId === $conv['id']);
                        @endphp
                        <!--begin::User/Group Item-->
                        <div
                            wire:click="selectConversation('{{ $conv['type'] }}', '{{ $conv['id'] }}')"
                            class="d-flex align-items-center p-3 rounded-3 mb-2 cursor-pointer transition-all {{ $isActive ? 'bg-light-primary border border-primary border-opacity-25' : 'hover-bg-light border border-transparent' }}"
                        >
                            <!-- Avatar -->
                            <div class="symbol symbol-45px symbol-circle me-3 position-relative flex-shrink-0">
                                @if($conv['type'] === 'group')
                                    <div class="symbol-label bg-light-warning text-warning fw-bold fs-4">
                                        <i class="ki-outline ki-people fs-2 text-warning"></i>
                                    </div>
                                @else
                                    @if($conv['avatar'])
                                        <img src="{{ asset('storage/'.$conv['avatar']) }}" alt="{{ $conv['name'] }}" />
                                    @else
                                        <div class="symbol-label bg-light-info text-info fw-bold fs-4">
                                            {{ $conv['initial'] }}
                                        </div>
                                    @endif
                                    @if($conv['is_online'])
                                        <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle w-10px h-10px"></span>
                                    @endif
                                @endif
                            </div>

                            <!-- Details -->
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h5 class="fs-6 fw-bold text-gray-900 text-truncate mb-0 {{ $isActive ? 'text-primary' : '' }}">
                                        {{ $conv['name'] }}
                                    </h5>
                                    @if($conv['latest_time'])
                                        <span class="fs-9 text-muted flex-shrink-0 ms-2">
                                            {{ $conv['latest_time']->isToday() ? $conv['latest_time']->format('H:i') : $conv['latest_time']->format('d/m') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-7 text-muted text-truncate me-2">
                                        {{ $conv['latest_message'] ?? $conv['subtitle'] }}
                                    </span>
                                    @if($conv['unread'] > 0)
                                        <span class="badge badge-sm badge-circle badge-primary flex-shrink-0">
                                            {{ $conv['unread'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!--end::User/Group Item-->
                    @empty
                        <div class="text-center py-10">
                            <i class="ki-outline ki-search-list fs-3x text-muted mb-3"></i>
                            <p class="fs-7 text-muted mb-0">Tidak ada kontak atau grup ditemukan.</p>
                        </div>
                    @endforelse
                </div>
                <!--end::List-->
            </div>
            <!--end::Card body-->
        </div>
    </div>
    <!--end::Sidebar-->

    <!--begin::Content (Chat Thread Box)-->
    <div class="flex-lg-row-fluid">
        <div class="card card-flush shadow-sm border border-gray-200 h-lg-100 d-flex flex-column" style="min-height: 650px;">
            @if($activeTarget)
                <!--begin::Card header-->
                <div class="card-header border-bottom border-gray-200 py-4">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-45px symbol-circle me-3">
                                @if($activeType === 'group')
                                    <div class="symbol-label bg-light-warning text-warning fw-bold fs-3">
                                        <i class="ki-outline ki-people fs-2 text-warning"></i>
                                    </div>
                                @else
                                    @if($activeTarget->photo)
                                        <img src="{{ asset('storage/'.$activeTarget->photo) }}" alt="{{ $activeTarget->full_name }}" />
                                    @else
                                        <div class="symbol-label bg-light-primary text-primary fw-bold fs-3">
                                            {{ strtoupper(substr($activeTarget->full_name ?? $activeTarget->username ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                @endif
                            </div>
                            <div>
                                <h4 class="fs-5 fw-bolder text-gray-900 mb-0">
                                    {{ $activeType === 'group' ? $activeTarget->name : ($activeTarget->full_name ?? $activeTarget->username) }}
                                </h4>
                                <div class="d-flex align-items-center fs-7 text-muted mt-0.5">
                                    @if($activeType === 'group')
                                        <span class="badge badge-light-warning fs-8 px-2 py-0.5 me-2">Grup Diskusi</span>
                                        <span>{{ $activeTarget->users->count() }} Anggota</span>
                                    @else
                                        <span class="w-8px h-8px bg-success rounded-circle me-2"></span>
                                        <span>{{ $activeTarget->position->name ?? $activeTarget->department->name ?? 'Online' }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex align-items-center gap-2">
                            @if($activeType === 'group')
                                <button
                                    type="button"
                                    wire:click="openGroupInfo"
                                    class="btn btn-sm btn-light-primary fw-semibold"
                                >
                                    <i class="ki-outline ki-information-5 fs-4 me-1"></i> Info Grup
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                <!--end::Card header-->

                <!--begin::Messages Thread Body-->
                <div
                    x-ref="messagesContainer"
                    x-init="scrollToBottom()"
                    class="card-body p-6 overflow-y-auto flex-grow-1"
                    style="max-height: calc(100vh - 420px); min-height: 400px; background-color: #f8fafc;"
                >
                    @forelse($messages as $msg)
                        @php
                            $isMe = ($msg->sender_id === auth()->id());
                        @endphp
                        <!--begin::Message Row-->
                        <div class="d-flex {{ $isMe ? 'justify-content-end' : 'justify-content-start' }} mb-5">
                            @if(!$isMe)
                                <!-- Other Sender Avatar -->
                                <div class="symbol symbol-35px symbol-circle me-3 mt-1 flex-shrink-0">
                                    @if($msg->sender->photo)
                                        <img src="{{ asset('storage/'.$msg->sender->photo) }}" alt="{{ $msg->sender->full_name }}" />
                                    @else
                                        <div class="symbol-label bg-light-info text-info fw-bold fs-7">
                                            {{ strtoupper(substr($msg->sender->full_name ?? $msg->sender->username ?? 'U', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="d-flex flex-column {{ $isMe ? 'align-items-end' : 'align-items-start' }}" style="max-width: 75%;">
                                @if(!$isMe && $activeType === 'group')
                                    <span class="fs-8 fw-bold text-gray-700 mb-1 ms-1">
                                        {{ $msg->sender->full_name ?? $msg->sender->username }}
                                    </span>
                                @endif

                                <!-- Message Bubble -->
                                <div class="p-4 rounded-3 shadow-xs {{ $isMe ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-800' }}">
                                    <!-- Text Content -->
                                    @if(!empty($msg->message))
                                        <div class="fs-6 leading-normal mb-2" style="word-break: break-word; white-space: pre-wrap;">{{ $msg->message }}</div>
                                    @endif

                                    <!-- Media: Image Attachment -->
                                    @if($msg->isImage() && $msg->attachment_path)
                                        <div class="mt-2 rounded-2 overflow-hidden border border-gray-100">
                                            <a href="{{ asset('storage/'.$msg->attachment_path) }}" target="_blank">
                                                <img
                                                    src="{{ asset('storage/'.$msg->attachment_path) }}"
                                                    alt="{{ $msg->attachment_name }}"
                                                    class="img-fluid rounded-2"
                                                    style="max-height: 280px; width: auto; object-fit: cover;"
                                                />
                                            </a>
                                        </div>
                                    @endif

                                    <!-- Media: Video Attachment -->
                                    @if($msg->isVideo() && $msg->attachment_path)
                                        <div class="mt-2 rounded-2 overflow-hidden bg-black">
                                            <video controls class="w-100 rounded-2" style="max-height: 280px;">
                                                <source src="{{ asset('storage/'.$msg->attachment_path) }}">
                                                Browser Anda tidak mendukung pemutar video.
                                            </video>
                                        </div>
                                    @endif

                                    <!-- Media: Document Attachment -->
                                    @if($msg->isDocument() && $msg->attachment_path)
                                        <div class="d-flex align-items-center p-3 rounded-2 mt-2 {{ $isMe ? 'bg-white bg-opacity-20 text-white' : 'bg-light border border-gray-200' }}">
                                            <i class="ki-outline ki-file-down fs-2x me-3 {{ $isMe ? 'text-white' : 'text-primary' }}"></i>
                                            <div class="flex-grow-1 min-w-0 me-3">
                                                <div class="fs-7 fw-bold text-truncate {{ $isMe ? 'text-white' : 'text-gray-900' }}">
                                                    {{ $msg->attachment_name ?? 'Dokumen Lampiran' }}
                                                </div>
                                                <div class="fs-9 {{ $isMe ? 'text-white text-opacity-75' : 'text-muted' }}">
                                                    {{ $msg->formattedAttachmentSize() }}
                                                </div>
                                            </div>
                                            <a
                                                href="{{ asset('storage/'.$msg->attachment_path) }}"
                                                download="{{ $msg->attachment_name }}"
                                                class="btn btn-sm btn-icon {{ $isMe ? 'btn-light text-primary' : 'btn-primary' }}"
                                                title="Unduh Berkas"
                                            >
                                                <i class="ki-outline ki-download fs-4"></i>
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <!-- Timestamp -->
                                <span class="fs-9 text-muted mt-1 px-1">
                                    {{ $msg->created_at->format('H:i') }}
                                    @if($isMe && $activeType === 'direct')
                                        <i class="ki-outline ki-double-check fs-8 ms-1 {{ $msg->read_at ? 'text-primary' : 'text-gray-400' }}"></i>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <!--end::Message Row-->
                    @empty
                        <div class="text-center py-20">
                            <div class="symbol symbol-65px symbol-circle bg-light-primary text-primary mx-auto mb-4">
                                <i class="ki-outline ki-message-text-2 fs-2tx"></i>
                            </div>
                            <h4 class="fs-5 fw-bold text-gray-800">Belum Ada Riwayat Percakapan</h4>
                            <p class="fs-7 text-muted">Kirim pesan pertama, berkas, gambar, atau emoji sekarang untuk memulai.</p>
                        </div>
                    @endforelse
                </div>
                <!--end::Messages Thread Body-->

                <!--begin::Card footer (Input Area)-->
                <div class="card-footer border-top border-gray-200 p-4 bg-white position-relative">
                    <!-- Attachment Preview Chip -->
                    @if($attachment)
                        <div class="d-flex align-items-center justify-content-between p-2 mb-3 bg-light-primary rounded-3 border border-primary border-opacity-25">
                            <div class="d-flex align-items-center text-truncate">
                                <i class="ki-outline ki-paperclip fs-3 text-primary me-2"></i>
                                <span class="fs-7 fw-semibold text-gray-900 text-truncate">
                                    {{ $attachment->getClientOriginalName() }}
                                </span>
                            </div>
                            <button
                                type="button"
                                wire:click="removeAttachment"
                                class="btn btn-sm btn-icon btn-light-danger ms-2"
                                title="Batal Lampiran"
                            >
                                <i class="ki-outline ki-cross fs-5"></i>
                            </button>
                        </div>
                    @endif

                    <!-- Emoji Picker Dropdown -->
                    <div
                        x-show="showEmojiPicker"
                        x-transition
                        @click.outside="showEmojiPicker = false"
                        class="position-absolute bottom-100 start-0 mb-3 ms-4 bg-white rounded-3 shadow-lg border border-gray-200 p-3 z-index-3"
                        style="width: 320px; max-height: 240px; overflow-y: auto;"
                    >
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="fs-7 fw-bold text-gray-800">Pilih Emoji</span>
                            <button type="button" @click="showEmojiPicker = false" class="btn btn-sm btn-icon btn-light">
                                <i class="ki-outline ki-cross fs-6"></i>
                            </button>
                        </div>
                        <div class="d-grid" style="grid-template-columns: repeat(8, 1fr); gap: 4px;">
                            <template x-for="emo in emojis" :key="emo">
                                <button
                                    type="button"
                                    @click="$wire.addEmoji(emo)"
                                    class="btn btn-sm p-1 fs-4 text-center border-0 hover-bg-light rounded"
                                    x-text="emo"
                                ></button>
                            </template>
                        </div>
                    </div>

                    <!-- Input Form -->
                    <form wire:submit="sendMessage" class="d-flex align-items-center gap-2">
                        <!-- Emoji Button -->
                        <button
                            type="button"
                            @click="showEmojiPicker = !showEmojiPicker"
                            class="btn btn-icon btn-sm btn-light-warning"
                            title="Emoji"
                        >
                            <i class="ki-outline ki-face-smile fs-2 text-warning"></i>
                        </button>

                        <!-- File Upload Button -->
                        <label class="btn btn-icon btn-sm btn-light-primary mb-0 cursor-pointer" title="Lampirkan File / Foto / Video">
                            <i class="ki-outline ki-paperclip fs-2 text-primary"></i>
                            <input
                                type="file"
                                wire:model="attachment"
                                class="d-none"
                                accept="image/*,video/*,application/pdf,.doc,.docx,.xls,.xlsx,.txt"
                            />
                        </label>

                        <!-- Textarea / Input -->
                        <div class="flex-grow-1 position-relative">
                            <input
                                type="text"
                                wire:model="messageText"
                                placeholder="Ketik pesan Anda di sini..."
                                class="form-control form-control-solid rounded-pill px-4 fs-7"
                                autofocus
                            />
                        </div>

                        <!-- Send Button -->
                        <button
                            type="submit"
                            class="btn btn-icon btn-primary rounded-circle shadow-sm"
                            title="Kirim Pesan"
                        >
                            <i class="ki-outline ki-send fs-2"></i>
                        </button>
                    </form>
                </div>
                <!--end::Card footer-->

            @else
                <!-- No Active Conversation Empty State -->
                <div class="d-flex flex-column flex-center text-center p-10 h-100 my-auto">
                    <div class="symbol symbol-100px symbol-circle bg-light-primary text-primary mb-5">
                        <i class="ki-outline ki-messages fs-3tx"></i>
                    </div>
                    <h3 class="fs-2 fw-bolder text-gray-900 mb-2">Selamat Datang di Portal Chat</h3>
                    <p class="fs-6 text-muted max-w-400px mb-6">
                        Pilih kontak rekan kerja atau grup obrolan dari daftar sebelah kiri untuk memulai percakapan instan.
                    </p>
                    <button
                        type="button"
                        wire:click="openNewGroupModal"
                        class="btn btn-primary fw-semibold"
                    >
                        <i class="ki-outline ki-user-edit fs-3 me-2"></i> Buat Grup Diskusi Baru
                    </button>
                </div>
            @endif
        </div>
    </div>
    <!--end::Content-->

    <!--begin::Modal Buat Grup Baru-->
    @if($showNewGroupModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered mw-500px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-4 px-6">
                        <h4 class="modal-title fw-bolder text-gray-900">
                            <i class="ki-outline ki-people fs-2 text-primary me-2"></i> Buat Grup Diskusi Baru
                        </h4>
                        <button
                            type="button"
                            wire:click="$set('showNewGroupModal', false)"
                            class="btn btn-sm btn-icon btn-light"
                        >
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <form wire:submit="createGroup">
                        <div class="modal-body py-5 px-6">
                            <!-- Group Name -->
                            <div class="mb-4">
                                <label class="form-label required fw-bold fs-7 text-gray-800">Nama Grup</label>
                                <input
                                    type="text"
                                    wire:model="newGroupName"
                                    class="form-control form-control-solid fs-7"
                                    placeholder="Contoh: Tim Maintenance Site A"
                                />
                                @error('newGroupName')
                                    <span class="text-danger fs-8 mt-1 d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Group Description -->
                            <div class="mb-4">
                                <label class="form-label fw-bold fs-7 text-gray-800">Deskripsi (Opsional)</label>
                                <textarea
                                    wire:model="newGroupDescription"
                                    class="form-control form-control-solid fs-7"
                                    rows="2"
                                    placeholder="Tuliskan tujuan dibuatnya grup ini..."
                                ></textarea>
                            </div>

                            <!-- Members Multi-select -->
                            <div class="mb-2">
                                <label class="form-label required fw-bold fs-7 text-gray-800">Pilih Anggota Tim</label>
                                <div class="border rounded-3 p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                                    @foreach($availableUsers as $user)
                                        <div class="form-check form-check-custom form-check-solid mb-2">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                wire:model="newGroupMembers"
                                                value="{{ $user->id }}"
                                                id="user_{{ $user->id }}"
                                            />
                                            <label class="form-check-label fs-7 fw-semibold text-gray-800 ms-2 cursor-pointer" for="user_{{ $user->id }}">
                                                {{ $user->full_name ?? $user->username }}
                                                <span class="text-muted fs-8">({{ $user->position->name ?? $user->department->name ?? 'Staff' }})</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('newGroupMembers')
                                    <span class="text-danger fs-8 mt-1 d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="modal-footer border-top py-3 px-6">
                            <button
                                type="button"
                                wire:click="$set('showNewGroupModal', false)"
                                class="btn btn-light fs-7"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                class="btn btn-primary fs-7 fw-bold"
                            >
                                <i class="ki-outline ki-check fs-4 me-1"></i> Buat Grup
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Buat Grup Baru-->

    <!--begin::Modal Info Grup-->
    @if($showGroupInfoModal && $activeType === 'group' && $activeTarget)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered mw-450px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-4 px-6">
                        <h4 class="modal-title fw-bolder text-gray-900">
                            <i class="ki-outline ki-information-5 fs-2 text-primary me-2"></i> Informasi Grup
                        </h4>
                        <button
                            type="button"
                            wire:click="$set('showGroupInfoModal', false)"
                            class="btn btn-sm btn-icon btn-light"
                        >
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <div class="modal-body py-5 px-6">
                        <div class="text-center mb-5">
                            <div class="symbol symbol-65px symbol-circle bg-light-warning text-warning mx-auto mb-3">
                                <i class="ki-outline ki-people fs-2tx"></i>
                            </div>
                            <h4 class="fs-5 fw-bold text-gray-900 mb-1">{{ $activeTarget->name }}</h4>
                            <p class="fs-7 text-muted mb-0">{{ $activeTarget->description ?? 'Tidak ada deskripsi.' }}</p>
                        </div>

                        <div class="separator my-4"></div>

                        <h5 class="fs-7 fw-bold text-gray-800 mb-3">
                            Daftar Anggota ({{ $activeTarget->users->count() }})
                        </h5>

                        <div class="border rounded-3 p-3 bg-light" style="max-height: 220px; overflow-y: auto;">
                            @foreach($activeTarget->users as $member)
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-gray-200">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-35px symbol-circle bg-light-primary text-primary me-3">
                                            <span class="fs-7 fw-bold">{{ strtoupper(substr($member->full_name ?? $member->username ?? 'U', 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <div class="fs-7 fw-bold text-gray-900">{{ $member->full_name ?? $member->username }}</div>
                                            <div class="fs-8 text-muted">{{ $member->position->name ?? $member->email }}</div>
                                        </div>
                                    </div>
                                    <span class="badge badge-sm {{ $member->pivot->role === 'admin' ? 'badge-light-danger' : 'badge-light-primary' }}">
                                        {{ ucfirst($member->pivot->role) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="modal-footer border-top py-3 px-6">
                        <button
                            type="button"
                            wire:click="$set('showGroupInfoModal', false)"
                            class="btn btn-primary fs-7 w-100"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Info Grup-->
</div>
