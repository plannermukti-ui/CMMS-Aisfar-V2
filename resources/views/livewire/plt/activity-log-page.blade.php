<div>
    <!--begin::Page Header-->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-6 gap-3">
        <div>
            <h1 class="fs-2x fw-bolder text-gray-900 mb-1">
                <i class="ki-outline ki-shield-tick fs-1 text-primary me-2"></i> Activity Log
            </h1>
            <span class="fs-7 text-muted fw-semibold">
                Rekam jejak seluruh aktivitas sistem — login, perubahan data, pembuatan WO, dll.
            </span>
        </div>
    </div>
    <!--end::Page Header-->

    <!--begin::Stats Row-->
    <div class="row g-4 mb-6">
        <div class="col-xl-3 col-md-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background:linear-gradient(135deg,#1a73e8,#0d47a1)">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">Total Log</span>
                        <i class="ki-outline ki-document fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ number_format($stats['total']) }}</div>
                    <div class="text-white opacity-75 fs-9">Semua Waktu</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background:linear-gradient(135deg,#ff8f00,#e65100)">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">Hari Ini</span>
                        <i class="ki-outline ki-calendar fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $stats['today'] }}</div>
                    <div class="text-white opacity-75 fs-9">{{ now()->format('d M Y') }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background:linear-gradient(135deg,#388e3c,#1b5e20)">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">Minggu Ini</span>
                        <i class="ki-outline ki-chart-line fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $stats['this_week'] }}</div>
                    <div class="text-white opacity-75 fs-9">7 Hari Terakhir</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background:linear-gradient(135deg,#455a64,#263238)">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">Pengguna Aktif</span>
                        <i class="ki-outline ki-people fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $stats['unique_users'] }}</div>
                    <div class="text-white opacity-75 fs-9">Dengan Aktivitas</div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Stats Row-->

    <!--begin::Log Table-->
    <div class="card card-flush shadow-sm border border-gray-200 bg-white">
        <!--begin::Card header-->
        <div class="card-header pt-5 pb-3 flex-wrap gap-3">
            <h3 class="card-title fw-bolder text-gray-900 fs-5">
                <i class="ki-outline ki-shield-tick fs-3 text-primary me-2"></i> Daftar Aktivitas
            </h3>
            <div class="card-toolbar d-flex flex-wrap gap-2">
                <!--begin::Search-->
                <div class="position-relative">
                    <i class="ki-outline ki-magnifier fs-4 position-absolute top-50 translate-middle-y ms-3 text-muted"></i>
                    <input
                        wire:model.live.debounce.400ms="search"
                        type="text"
                        class="form-control form-control-solid form-control-sm ps-9 w-200px fs-7"
                        placeholder="Cari modul, aksi, user..."
                    />
                </div>

                <!--begin::Module Filter-->
                <select wire:model.live="filterModule" class="form-select form-select-solid form-select-sm w-150px fs-7">
                    <option value="all">Semua Modul</option>
                    @foreach($modules as $mod)
                        <option value="{{ $mod }}">{{ ucwords(str_replace('-', ' ', $mod)) }}</option>
                    @endforeach
                </select>

                <!--begin::Action Filter-->
                <select wire:model.live="filterAction" class="form-select form-select-solid form-select-sm w-130px fs-7">
                    <option value="all">Semua Aksi</option>
                    @foreach($actions as $act)
                        <option value="{{ $act }}">{{ ucfirst($act) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed align-middle gy-3 gs-3 fs-7">
                    <thead class="bg-light text-muted fw-bold fs-8 text-uppercase">
                        <tr>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Modul</th>
                            <th>Aksi</th>
                            <th>Record</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-700">
                        @forelse($logs as $log)
                            <tr wire:key="log-{{ $log->id }}">
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-900 fw-bold fs-7">{{ $log->created_at->format('d M Y') }}</span>
                                        <span class="text-muted fs-9">{{ $log->created_at->format('H:i:s') }}</span>
                                        <span class="text-muted fs-9">{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="symbol symbol-32px symbol-circle">
                                            <div class="symbol-label fs-9 fw-bolder bg-primary text-white">
                                                {{ strtoupper(substr($log->user?->full_name ?? 'S', 0, 2)) }}
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-gray-900 fs-8">{{ $log->user?->full_name ?? 'System' }}</span>
                                            <span class="text-muted fs-9">{{ $log->user?->email ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light-primary fw-semibold fs-9">
                                        {{ ucwords(str_replace('-', ' ', $log->module)) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $actionClasses = [
                                            'created' => 'badge-light-success',
                                            'updated' => 'badge-light-warning',
                                            'deleted' => 'badge-light-danger',
                                            'viewed' => 'badge-light-info',
                                            'login' => 'badge-light-primary',
                                            'logout' => 'badge-light-dark',
                                        ];
                                        $badgeClass = $actionClasses[$log->action] ?? 'badge-light-secondary';
                                    @endphp
                                    <span class="badge {{ $badgeClass }} fw-bold fs-9">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->model_id)
                                        <span class="text-muted fs-9 font-monospace">
                                            {{ class_basename($log->model_type ?? '') }}
                                        </span>
                                        <span class="text-muted fs-9 d-block text-truncate" style="max-width:120px;" title="{{ $log->model_id }}">
                                            #{{ substr($log->model_id, 0, 8) }}…
                                        </span>
                                    @else
                                        <span class="text-muted fs-9">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted fs-9 font-monospace">{{ $log->ip_address ?? '—' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10">
                                    <i class="ki-outline ki-shield-tick fs-3x text-gray-300 mb-3 d-block"></i>
                                    <span class="text-muted fs-7">Tidak ada log yang cocok dengan filter.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!--begin::Pagination-->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4">
                <div class="text-muted fs-8">
                    Menampilkan {{ $logs->firstItem() ?? 0 }}–{{ $logs->lastItem() ?? 0 }}
                    dari {{ $logs->total() }} log
                </div>
                {{ $logs->links() }}
            </div>
            <!--end::Pagination-->
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Log Table-->
</div>
