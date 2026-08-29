<x-filament-panels::page>
    {{-- ── Stats Row ─────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-6">

        {{-- Total Log --}}
        <div class="rounded-xl p-5 text-white shadow-sm"
             style="background: linear-gradient(135deg,#1a73e8,#0d47a1)">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider opacity-80">Total Log</span>
                <x-heroicon-o-document-text class="w-6 h-6 opacity-50" />
            </div>
            <div class="text-4xl font-black">{{ number_format($stats['total']) }}</div>
            <div class="text-xs opacity-75 mt-1">Semua Waktu</div>
        </div>

        {{-- Hari Ini --}}
        <div class="rounded-xl p-5 text-white shadow-sm"
             style="background: linear-gradient(135deg,#ff8f00,#e65100)">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider opacity-80">Hari Ini</span>
                <x-heroicon-o-calendar class="w-6 h-6 opacity-50" />
            </div>
            <div class="text-4xl font-black">{{ $stats['today'] }}</div>
            <div class="text-xs opacity-75 mt-1">{{ now()->format('d M Y') }}</div>
        </div>

        {{-- Minggu Ini --}}
        <div class="rounded-xl p-5 text-white shadow-sm"
             style="background: linear-gradient(135deg,#388e3c,#1b5e20)">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider opacity-80">Minggu Ini</span>
                <x-heroicon-o-chart-bar class="w-6 h-6 opacity-50" />
            </div>
            <div class="text-4xl font-black">{{ $stats['this_week'] }}</div>
            <div class="text-xs opacity-75 mt-1">7 Hari Terakhir</div>
        </div>

        {{-- Pengguna Aktif --}}
        <div class="rounded-xl p-5 text-white shadow-sm"
             style="background: linear-gradient(135deg,#455a64,#263238)">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider opacity-80">Pengguna Aktif</span>
                <x-heroicon-o-users class="w-6 h-6 opacity-50" />
            </div>
            <div class="text-4xl font-black">{{ $stats['unique_users'] }}</div>
            <div class="text-xs opacity-75 mt-1">Dengan Aktivitas</div>
        </div>
    </div>

    {{-- ── Filters ─────────────────────────────────────────────── --}}
    <div class="fi-card rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 mb-5">
        <div class="flex flex-wrap gap-3 items-center">
            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px]">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                <input
                    wire:model.live.debounce.400ms="search"
                    type="text"
                    placeholder="Cari modul, aksi, atau nama user..."
                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500"
                />
            </div>

            {{-- Module Filter --}}
            <select wire:model.live="filterModule"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 min-w-[150px]">
                <option value="all">Semua Modul</option>
                @foreach($modules as $mod)
                    <option value="{{ $mod }}">{{ ucwords(str_replace('-', ' ', $mod)) }}</option>
                @endforeach
            </select>

            {{-- Action Filter --}}
            <select wire:model.live="filterAction"
                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 min-w-[130px]">
                <option value="all">Semua Aksi</option>
                @foreach($actions as $act)
                    <option value="{{ $act }}">{{ ucfirst($act) }}</option>
                @endforeach
            </select>

            {{-- Result count --}}
            <span class="text-sm text-gray-500 dark:text-gray-400 ml-auto whitespace-nowrap">
                {{ $logs->total() }} log ditemukan
            </span>
        </div>
    </div>

    {{-- ── Log Table ─────────────────────────────────────────── --}}
    <div class="fi-card rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Waktu</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pengguna</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Modul</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Record</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($logs as $log)
                        <tr wire:key="log-{{ $log->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-semibold text-gray-900 dark:text-gray-100 text-xs">{{ $log->created_at->format('d M Y, H:i') }}</div>
                                <div class="text-gray-400 text-xs mt-0.5">{{ $log->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                         style="background:linear-gradient(135deg,#1a73e8,#0d47a1)">
                                        {{ strtoupper(substr($log->user?->full_name ?? 'S', 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 dark:text-gray-100 text-xs">{{ $log->user?->full_name ?? 'System' }}</div>
                                        <div class="text-gray-400 text-xs">{{ $log->user?->email ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ ucwords(str_replace('-', ' ', $log->module)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $badgeColor = match($log->action) {
                                        'created' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                        'updated' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                        'deleted' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                        'viewed'  => 'bg-sky-100 text-sky-800 dark:bg-sky-900 dark:text-sky-200',
                                        'login'   => 'bg-violet-100 text-violet-800 dark:bg-violet-900 dark:text-violet-200',
                                        'logout'  => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                        default   => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold {{ $badgeColor }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($log->model_id)
                                    <div class="text-xs text-gray-600 dark:text-gray-400 font-mono">{{ class_basename($log->model_type ?? '') }}</div>
                                    <div class="text-xs text-gray-400 font-mono truncate max-w-[100px]" title="{{ $log->model_id }}">#{{ substr($log->model_id, 0, 8) }}…</div>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $log->ip_address ?? '—' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <x-heroicon-o-shield-check class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" />
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada log yang cocok dengan filter saat ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</x-filament-panels::page>
