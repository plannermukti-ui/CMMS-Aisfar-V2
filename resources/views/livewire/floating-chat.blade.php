<div
    x-data="{
        isOpen: @entangle('isOpen'),
        isDragging: false,
        hasDragged: false,
        startX: 0,
        startY: 0,
        currentX: 0,
        currentY: 0,
        xOffset: 0,
        yOffset: 0,
        searchQuery: '',

        init() {
            const savedPos = localStorage.getItem('cmms_chat_pos');
            if (savedPos) {
                try {
                    const pos = JSON.parse(savedPos);
                    this.xOffset = pos.x || 0;
                    this.yOffset = pos.y || 0;
                    this.applyTransform();
                } catch(e) {
                    localStorage.removeItem('cmms_chat_pos');
                }
            }
        },

        startDrag(e) {
            if (e.target.closest('.no-drag')) return;
            
            this.isDragging = true;
            this.hasDragged = false;
            
            const clientX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
            const clientY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;
            
            this.startX = clientX - this.xOffset;
            this.startY = clientY - this.yOffset;
        },

        onDrag(e) {
            if (!this.isDragging) return;
            
            const clientX = e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
            const clientY = e.type.includes('touch') ? e.touches[0].clientY : e.clientY;
            
            const newX = clientX - this.startX;
            const newY = clientY - this.startY;
            
            if (Math.abs(newX - this.xOffset) > 4 || Math.abs(newY - this.yOffset) > 4) {
                this.hasDragged = true;
            }
            
            this.xOffset = newX;
            this.yOffset = newY;
            this.applyTransform();
        },

        endDrag() {
            if (!this.isDragging) return;
            this.isDragging = false;
            
            // Constrain within viewport
            const rect = this.$refs.widgetContainer.getBoundingClientRect();
            const winW = window.innerWidth;
            const winH = window.innerHeight;
            
            if (rect.right < 80) this.xOffset += (80 - rect.right);
            if (rect.left > winW - 80) this.xOffset -= (rect.left - (winW - 80));
            if (rect.bottom < 80) this.yOffset += (80 - rect.bottom);
            if (rect.top > winH - 80) this.yOffset -= (rect.top - (winH - 80));
            
            this.applyTransform();
            localStorage.setItem('cmms_chat_pos', JSON.stringify({ x: this.xOffset, y: this.yOffset }));
        },

        applyTransform() {
            this.$refs.widgetContainer.style.transform = `translate3d(${this.xOffset}px, ${this.yOffset}px, 0)`;
        },

        handleClick() {
            if (!this.hasDragged) {
                $wire.toggleChat();
            }
        },

        resetPosition() {
            this.xOffset = 0;
            this.yOffset = 0;
            this.applyTransform();
            localStorage.removeItem('cmms_chat_pos');
        },

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
    @mousemove.window="onDrag($event)"
    @mouseup.window="endDrag()"
    @touchmove.window="onDrag($event)"
    @touchend.window="endDrag()"
    x-ref="widgetContainer"
    class="fixed bottom-6 right-6 z-[9999] select-none"
    style="touch-action: none;"
>
    <!-- Floating Trigger Button -->
    <div
        x-show="!isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        @mousedown="startDrag($event)"
        @touchstart="startDrag($event)"
        @click="handleClick()"
        class="group relative flex items-center justify-center cursor-grab active:cursor-grabbing"
    >
        <!-- Outer Glow Ring -->
        <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-500 rounded-full blur opacity-40 group-hover:opacity-75 transition duration-300"></div>

        <!-- Main Button Circle -->
        <button
            type="button"
            class="relative w-14 h-14 bg-gradient-to-tr from-blue-600 to-blue-500 text-white rounded-full shadow-2xl flex items-center justify-center transition-all duration-300 group-hover:scale-105 border-2 border-white/20"
        >
            <i class="ki-outline ki-messages text-white fs-1"></i>
            
            <!-- Online Dot -->
            <span class="absolute top-1 right-1 flex h-3.5 w-3.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-white"></span>
            </span>
        </button>

        <!-- Drag Grip Tooltip Indicator -->
        <div class="absolute -top-8 px-2 py-1 bg-gray-900/90 backdrop-blur-sm text-white text-[10px] font-medium rounded-md shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
            Klik / Geser Pesan
        </div>
    </div>

    <!-- Active Chat Window Container -->
    <div
        x-show="isOpen"
        x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-300"
        x-transition:enter-start="opacity-0 translate-y-8 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-8 scale-95"
        class="w-[360px] sm:w-[400px] h-[560px] max-h-[85vh] bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col font-sans"
        style="box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);"
    >
        <!-- Header (Draggable Handle) -->
        <div
            @mousedown="startDrag($event)"
            @touchstart="startDrag($event)"
            class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white px-4 py-3.5 flex items-center justify-between cursor-grab active:cursor-grabbing select-none border-b border-white/10"
        >
            <div class="flex items-center space-x-3 min-w-0">
                @if($selectedUser)
                    <button
                        type="button"
                        @click="$wire.goBack()"
                        class="no-drag w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors"
                        title="Kembali ke Daftar Kontak"
                    >
                        <i class="ki-outline ki-arrow-left fs-3"></i>
                    </button>
                    <div class="relative">
                        <div class="w-8 h-8 rounded-full bg-blue-500 text-white font-bold flex items-center justify-center text-xs shadow-inner">
                            {{ strtoupper(substr($selectedUser->full_name ?? $selectedUser->name ?? 'U', 0, 1)) }}
                        </div>
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-slate-900 rounded-full"></span>
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-bold text-sm text-white truncate leading-tight">
                            {{ $selectedUser->full_name ?? $selectedUser->name }}
                        </h4>
                        <p class="text-[11px] text-emerald-400 font-medium leading-none mt-0.5">
                            Online
                        </p>
                    </div>
                @else
                    <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md">
                        <i class="ki-outline ki-messages fs-3"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-white leading-tight">
                            Chat Kolaborasi
                        </h4>
                        <p class="text-[11px] text-gray-300 font-medium leading-none mt-0.5">
                            Internal Team Messenger
                        </p>
                    </div>
                @endif
            </div>

            <!-- Header Action Controls -->
            <div class="flex items-center space-x-1 no-drag">
                <!-- Reset Position Icon -->
                <button
                    type="button"
                    @click="resetPosition()"
                    class="w-7 h-7 rounded-lg text-gray-400 hover:text-white hover:bg-white/10 flex items-center justify-center transition"
                    title="Reset Posisi Pojok"
                >
                    <i class="ki-outline ki-arrows-circle fs-4"></i>
                </button>
                <!-- Minimize Button -->
                <button
                    type="button"
                    @click="$wire.toggleChat()"
                    class="w-7 h-7 rounded-lg text-gray-400 hover:text-white hover:bg-white/10 flex items-center justify-center transition"
                    title="Tutup"
                >
                    <i class="ki-outline ki-cross fs-3"></i>
                </button>
            </div>
        </div>

        <!-- Body Area -->
        <div class="flex-1 flex flex-col overflow-hidden bg-slate-50">
            @if(!$selectedUser)
                <!-- Search Contact Input -->
                <div class="p-3 bg-white border-b border-gray-100">
                    <div class="relative">
                        <i class="ki-outline ki-magnifier fs-4 absolute left-3 top-2.5 text-gray-400"></i>
                        <input
                            type="text"
                            x-model="searchQuery"
                            placeholder="Cari anggota tim..."
                            class="w-full pl-9 pr-3 py-1.5 bg-gray-100 text-gray-800 text-xs rounded-xl border-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition"
                        />
                    </div>
                </div>

                <!-- User Contacts List -->
                <div class="flex-1 overflow-y-auto p-2 space-y-1">
                    @forelse($users as $u)
                        <div
                            x-show="!searchQuery || '{{ strtolower($u->full_name ?? $u->name ?? '') }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($u->email ?? '') }}'.includes(searchQuery.toLowerCase())"
                            @click="$wire.selectUser('{{ $u->id }}')"
                            class="flex items-center space-x-3 p-2.5 rounded-xl hover:bg-white hover:shadow-sm cursor-pointer transition-all duration-150 border border-transparent hover:border-gray-100 group"
                        >
                            <div class="relative flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-100 to-indigo-100 text-blue-700 font-bold flex items-center justify-center text-sm group-hover:from-blue-600 group-hover:to-indigo-600 group-hover:text-white transition-all shadow-sm">
                                    {{ strtoupper(substr($u->full_name ?? $u->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h5 class="text-xs font-bold text-gray-900 truncate group-hover:text-blue-600 transition-colors">
                                        {{ $u->full_name ?? $u->name }}
                                    </h5>
                                </div>
                                <p class="text-[11px] text-gray-500 truncate mt-0.5">
                                    {{ $u->position->name ?? $u->email }}
                                </p>
                            </div>
                            <i class="ki-outline ki-right fs-4 text-gray-400 group-hover:text-blue-500 transition-colors"></i>
                        </div>
                    @empty
                        <div class="text-center py-12 px-4">
                            <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                                <i class="ki-outline ki-user-square fs-1"></i>
                            </div>
                            <p class="text-xs font-medium text-gray-500">Tidak ada kontak yang ditemukan</p>
                        </div>
                    @endforelse
                </div>

            @else
                <!-- Message Thread Header Info & Messages List -->
                <div
                    x-ref="messagesContainer"
                    wire:poll.4s="loadMessages"
                    class="flex-1 overflow-y-auto p-4 space-y-3 bg-[#f8fafc]"
                    x-init="scrollToBottom()"
                >
                    <div class="text-center my-2">
                        <span class="px-3 py-1 bg-white border border-gray-200/80 rounded-full text-[10px] font-semibold text-gray-500 shadow-2xs">
                            Percakapan Terenkripsi Internal
                        </span>
                    </div>

                    @forelse($messages as $msg)
                        @if($msg->sender_id === auth()->id())
                            <!-- Message Sent by Me -->
                            <div class="flex flex-col items-end">
                                <div class="bg-gradient-to-tr from-blue-600 to-blue-500 text-white rounded-2xl rounded-tr-sm px-4 py-2.5 max-w-[82%] text-xs shadow-md shadow-blue-500/10 leading-relaxed break-words">
                                    {{ $msg->message }}
                                </div>
                                <span class="text-[10px] text-gray-400 mt-1 font-medium mr-1">
                                    {{ $msg->created_at->format('H:i') }}
                                </span>
                            </div>
                        @else
                            <!-- Message Received from Other -->
                            <div class="flex flex-col items-start">
                                <div class="bg-white border border-gray-200/80 text-gray-800 rounded-2xl rounded-tl-sm px-4 py-2.5 max-w-[82%] text-xs shadow-sm leading-relaxed break-words">
                                    {{ $msg->message }}
                                </div>
                                <span class="text-[10px] text-gray-400 mt-1 font-medium ml-1">
                                    {{ $msg->created_at->format('H:i') }}
                                </span>
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-16">
                            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center mx-auto mb-3">
                                <i class="ki-outline ki-message-text-2 fs-1"></i>
                            </div>
                            <p class="text-xs font-semibold text-gray-700">Mulai Percakapan Baru</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">Kirim pesan kepada {{ $selectedUser->full_name ?? $selectedUser->name }}</p>
                        </div>
                    @endforelse
                </div>

                <!-- Input Message Box -->
                <div class="p-3 bg-white border-t border-gray-200/70">
                    <form
                        @submit.prevent="$wire.sendMessage(); scrollToBottom()"
                        class="flex items-center space-x-2"
                    >
                        <div class="relative flex-1">
                            <input
                                type="text"
                                wire:model="messageText"
                                placeholder="Tulis pesan Anda..."
                                class="w-full py-2 px-4 bg-gray-100 border-none rounded-full text-xs text-gray-800 focus:bg-white focus:ring-2 focus:ring-blue-500 transition placeholder-gray-400"
                                autofocus
                            />
                        </div>
                        <button
                            type="submit"
                            class="w-9 h-9 rounded-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center shadow-md hover:shadow-lg transition-all duration-200 flex-shrink-0 disabled:opacity-50"
                        >
                            <i class="ki-outline ki-send fs-4"></i>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
