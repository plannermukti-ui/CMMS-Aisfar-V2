<div>
    <!--begin::Page Header-->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-6 gap-3">
        <div>
            <h1 class="fs-2hx fw-bolder text-gray-900 mb-1">
                <i class="ki-outline ki-wrench fs-1 text-primary me-2"></i> PLANT Maintenance & Operations Dashboard
            </h1>
            <span class="fs-7 text-muted fw-semibold">Pusat monitoring operasional alat berat, penanganan breakdown, penjadwalan service, dan pemantauan Work Order.</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('plt.workorder') }}" class="btn btn-sm btn-primary fw-bold">
                <i class="ki-outline ki-plus fs-4 me-1"></i> Buat Work Order Baru
            </a>
            <a href="{{ route('scm.mol') }}" class="btn btn-sm btn-light-success fw-bold">
                <i class="ki-outline ki-handcart fs-4 me-1"></i> Request Part (MOL)
            </a>
        </div>
    </div>
    <!--end::Page Header-->

    <!--begin::KPI Stats Row-->
    <div class="row g-5 g-xl-8 mb-6">
        <!-- Total Equipments -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4">
                    <span class="badge badge-light-primary text-primary fw-bold fs-8 mb-2">Total Unit / Asset</span>
                    <div class="fs-2hx fw-bolder text-gray-900">{{ $kpis['total_equipments'] }}</div>
                    <div class="fs-9 text-muted">Alat Berat Terdaftar</div>
                </div>
            </div>
        </div>

        <!-- Active Work Orders -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4">
                    <span class="badge badge-light-warning text-warning fw-bold fs-8 mb-2">WO Aktif</span>
                    <div class="fs-2hx fw-bolder text-warning">{{ $kpis['active_wo'] }}</div>
                    <div class="fs-9 text-muted">Sedang Dikerjakan</div>
                </div>
            </div>
        </div>

        <!-- Breakdown Unit -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4">
                    <span class="badge badge-light-danger text-danger fw-bold fs-8 mb-2">Breakdown Unit</span>
                    <div class="fs-2hx fw-bolder text-danger">{{ $kpis['breakdown_wo'] }}</div>
                    <div class="fs-9 text-muted">Kebutuhan Perbaikan Darurat</div>
                </div>
            </div>
        </div>

        <!-- Waiting Spareparts -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4">
                    <span class="badge badge-light-info text-info fw-bold fs-8 mb-2">Waiting Part</span>
                    <div class="fs-2hx fw-bolder text-info">{{ $kpis['waiting_spareparts'] }}</div>
                    <div class="fs-9 text-muted">Menunggu Kedatangan Part</div>
                </div>
            </div>
        </div>

        <!-- SCM MOL Requests -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4">
                    <span class="badge badge-light-success text-success fw-bold fs-8 mb-2">MOL di SCM</span>
                    <div class="fs-2hx fw-bolder text-success">{{ $kpis['mol_requests'] }}</div>
                    <div class="fs-9 text-muted">Order Suku Cadang Aktif</div>
                </div>
            </div>
        </div>

        <!-- Completed Work Orders -->
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4">
                    <span class="badge badge-light-secondary text-gray-700 fw-bold fs-8 mb-2">WO Selesai</span>
                    <div class="fs-2hx fw-bolder text-gray-800">{{ $kpis['completed_wo'] }}</div>
                    <div class="fs-9 text-muted">Closed Maintenance</div>
                </div>
            </div>
        </div>
    </div>
    <!--end::KPI Stats Row-->

    <!--begin::Quick Action Banners-->
    <div class="row g-5 g-xl-8 mb-6">
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('plt.workorder') }}" class="card card-flush shadow-xs border border-gray-200 h-100 bg-white text-hover-primary p-4 d-flex flex-row align-items-center">
                <div class="symbol symbol-45px symbol-circle bg-light-primary text-primary me-3">
                    <i class="ki-outline ki-wrench fs-2"></i>
                </div>
                <div>
                    <div class="fw-bolder text-gray-900 fs-6">Kelola Work Order</div>
                    <span class="fs-9 text-muted">Multi-Problem & Action Hierarki</span>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('scm.mol') }}" class="card card-flush shadow-xs border border-gray-200 h-100 bg-white text-hover-success p-4 d-flex flex-row align-items-center">
                <div class="symbol symbol-45px symbol-circle bg-light-success text-success me-3">
                    <i class="ki-outline ki-handcart fs-2"></i>
                </div>
                <div>
                    <div class="fw-bolder text-gray-900 fs-6">Request Sparepart (MOL)</div>
                    <span class="fs-9 text-muted">Cek Stok & Auto-Generate PR</span>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('chat') }}" class="card card-flush shadow-xs border border-gray-200 h-100 bg-white text-hover-info p-4 d-flex flex-row align-items-center">
                <div class="symbol symbol-45px symbol-circle bg-light-info text-info me-3">
                    <i class="ki-outline ki-messages fs-2"></i>
                </div>
                <div>
                    <div class="fw-bolder text-gray-900 fs-6">Messenger & Group</div>
                    <span class="fs-9 text-muted">Koordinasi Mekanik & Logistik</span>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('profile') }}" class="card card-flush shadow-xs border border-gray-200 h-100 bg-white text-hover-warning p-4 d-flex flex-row align-items-center">
                <div class="symbol symbol-45px symbol-circle bg-light-warning text-warning me-3">
                    <i class="ki-outline ki-profile-circle fs-2"></i>
                </div>
                <div>
                    <div class="fw-bolder text-gray-900 fs-6">Profil & Kinerja Mekanik</div>
                    <span class="fs-9 text-muted">Penugasan Subtask & Riwayat</span>
                </div>
            </a>
        </div>
    </div>
    <!--end::Quick Action Banners-->

    <!--begin::Plant Engineering Modules Row-->
    <div class="row g-5 g-xl-8 mb-6">
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('plt.components') }}" class="card card-flush shadow-xs border border-gray-200 h-100 bg-white text-hover-primary p-4 d-flex flex-row align-items-center">
                <div class="symbol symbol-45px symbol-circle bg-light-primary text-primary me-3">
                    <i class="ki-outline ki-cube-2 fs-2"></i>
                </div>
                <div>
                    <div class="fw-bolder text-gray-900 fs-6">Component Tracker</div>
                    <span class="fs-9 text-muted">Silsilah, Rotasi & Life Meter</span>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('plt.ccr') }}" class="card card-flush shadow-xs border border-gray-200 h-100 bg-white text-hover-info p-4 d-flex flex-row align-items-center">
                <div class="symbol symbol-45px symbol-circle bg-light-info text-info me-3">
                    <i class="ki-outline ki-clipboard fs-2"></i>
                </div>
                <div>
                    <div class="fw-bolder text-gray-900 fs-6">Kondisi Fisik (CCR)</div>
                    <span class="fs-9 text-muted">Evaluasi Keausan & Sisa Umur</span>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('plt.far') }}" class="card card-flush shadow-xs border border-gray-200 h-100 bg-white text-hover-danger p-4 d-flex flex-row align-items-center">
                <div class="symbol symbol-45px symbol-circle bg-light-danger text-danger me-3">
                    <i class="ki-outline ki-shield-cross fs-2"></i>
                </div>
                <div>
                    <div class="fw-bolder text-gray-900 fs-6">Investigasi Kerusakan (FAR)</div>
                    <span class="fs-9 text-muted">Root Cause 5-Why & CAPA</span>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('plt.osr') }}" class="card card-flush shadow-xs border border-gray-200 h-100 bg-white text-hover-success p-4 d-flex flex-row align-items-center">
                <div class="symbol symbol-45px symbol-circle bg-light-success text-success me-3">
                    <i class="ki-outline ki-delivery-3 fs-2"></i>
                </div>
                <div>
                    <div class="fw-bolder text-gray-900 fs-6">Perbaikan Luar (OSR)</div>
                    <span class="fs-9 text-muted">Vendor Bubut & QC Garansi</span>
                </div>
            </a>
        </div>
    </div>
    <!--end::Plant Engineering Modules Row-->

    <!--begin::Recent Work Orders & Equipment Overview Row-->
    <div class="row g-5 g-xl-8 mb-6">
        <!-- Recent Work Orders -->
        <div class="col-xl-8">
            <div class="card card-flush shadow-sm border border-gray-200 h-100 bg-white">
                <div class="card-header pt-5 pb-3">
                    <h3 class="card-title fw-bolder text-gray-900 fs-5">
                        <i class="ki-outline ki-wrench fs-3 text-primary me-2"></i> Aktivitas Work Order Terkini (PLANT)
                    </h3>
                    <div class="card-toolbar">
                        <a href="{{ route('plt.workorder') }}" class="btn btn-sm btn-light-primary fw-bold fs-8">Lihat Semua WO</a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 fs-7">
                            <thead>
                                <tr class="fw-bold text-muted fs-8 text-uppercase">
                                    <th>No. WO</th>
                                    <th>Unit & Model</th>
                                    <th>Pekerjaan / Problem</th>
                                    <th>Mekanik Bertugas</th>
                                    <th>Tipe & Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentWorkOrders as $wo)
                                    <tr>
                                        <td>
                                            <a href="{{ route('plt.workorder') }}" class="text-gray-900 fw-bold text-hover-primary">
                                                {{ $wo->wo_number }}
                                            </a>
                                            <span class="text-muted fs-9 d-block">{{ $wo->created_at->format('d M Y') }}</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-900 fw-bold">{{ $wo->equipment->unit ?? 'Unit' }}</span>
                                            <span class="text-muted fs-9 d-block">{{ $wo->equipment->reffEquip->model ?? '-' }} ({{ $wo->equipment->site->site_name ?? '-' }})</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-semibold d-block text-truncate" style="max-width: 220px;" title="{{ $wo->job_title }}">
                                                {{ $wo->job_title }}
                                            </span>
                                            <span class="badge badge-light-secondary fs-9 mt-1">{{ $wo->tasks->count() }} Problem Log</span>
                                        </td>
                                        <td>
                                            @php
                                                $mechanics = collect();
                                                foreach($wo->tasks as $t) {
                                                    foreach($t->subtasks as $st) {
                                                        foreach($st->mechanics as $m) {
                                                            $mechanics->push($m->full_name ?? $m->username);
                                                        }
                                                    }
                                                }
                                                $mechanics = $mechanics->unique();
                                            @endphp
                                            @if($mechanics->count() > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($mechanics->take(2) as $mech)
                                                        <span class="badge badge-light-info fs-9">{{ $mech }}</span>
                                                    @endforeach
                                                    @if($mechanics->count() > 2)
                                                        <span class="badge badge-light-secondary fs-9">+{{ $mechanics->count() - 2 }}</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted fs-9">Belum ditugaskan</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-light-{{ $wo->job_type === 'breakdown' ? 'danger' : ($wo->job_type === 'preventive' ? 'info' : 'primary') }} fs-9 fw-bold">
                                                {{ ucfirst($wo->job_type) }}
                                            </span>
                                            <span class="badge badge-light-{{ $wo->status === 'open' ? 'warning' : ($wo->status === 'in_progress' ? 'primary' : 'success') }} fs-9 fw-bold mt-1 d-inline-block">
                                                {{ ucfirst(str_replace('_', ' ', $wo->status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-6 text-muted fs-8">Belum ada aktivitas Work Order.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Equipment Fleet Overview -->
        <div class="col-xl-4">
            <div class="card card-flush shadow-sm border border-gray-200 h-100 bg-white">
                <div class="card-header pt-5 pb-3">
                    <h3 class="card-title fw-bolder text-gray-900 fs-5">
                        <i class="ki-outline ki-truck fs-3 text-success me-2"></i> Populasi Unit Alat Berat
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 fs-7">
                            <thead>
                                <tr class="fw-bold text-muted fs-8 text-uppercase">
                                    <th>Unit</th>
                                    <th>Model & Kategori</th>
                                    <th>Lokasi Site</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($equipmentOverview as $eq)
                                    <tr>
                                        <td class="fw-bold text-gray-900">
                                            <span class="badge badge-light-dark fs-8">{{ $eq->unit }}</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-semibold fs-8 d-block">{{ $eq->reffEquip->model ?? 'Model' }}</span>
                                            <span class="text-muted fs-9">{{ $eq->reffEquip->category ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-primary fs-9">{{ $eq->site->site_name ?? 'Default Site' }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-6 text-muted fs-8">Belum ada unit terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Recent Work Orders-->
</div>
