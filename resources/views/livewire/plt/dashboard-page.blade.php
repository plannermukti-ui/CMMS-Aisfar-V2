<div>
    <!--begin::Page Header-->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-6 gap-3">
        <div>
            <h1 class="fs-2x fw-bolder text-gray-900 mb-1">
                <i class="ki-outline ki-wrench fs-1 text-primary me-2"></i> Plant Maintenance Dashboard
            </h1>
            <span class="fs-7 text-muted fw-semibold">
                Monitoring operasional alat berat, penanganan breakdown, penjadwalan servis & Work Order.
                <span class="badge badge-light-primary ms-2 fs-9">{{ now()->format('d M Y, H:i') }} WIB</span>
            </span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('plt.workorder') }}" class="btn btn-sm btn-primary fw-bold">
                <i class="ki-outline ki-plus fs-4 me-1"></i> Buat Work Order
            </a>
            <a href="{{ route('scm.mol') }}" class="btn btn-sm btn-light-success fw-bold">
                <i class="ki-outline ki-handcart fs-4 me-1"></i> Request Part (MOL)
            </a>
        </div>
    </div>
    <!--end::Page Header-->

    <!--begin::KPI Stats Row-->
    <div class="row g-4 mb-6">
        <!-- Total Unit -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">Total Unit</span>
                        <i class="ki-outline ki-truck fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $kpis['total_equipments'] }}</div>
                    <div class="text-white opacity-75 fs-9">Alat Berat Terdaftar</div>
                </div>
            </div>
        </div>

        <!-- WO Aktif -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background: linear-gradient(135deg, #ff8f00 0%, #e65100 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">WO Aktif</span>
                        <i class="ki-outline ki-wrench fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $kpis['active_wo'] }}</div>
                    <div class="d-flex gap-2 mt-1">
                        <span class="badge fs-9 text-white" style="background:rgba(255,255,255,0.2);">Open: {{ $kpis['open_wo'] }}</span>
                        <span class="badge fs-9 text-white" style="background:rgba(255,255,255,0.2);">Proses: {{ $kpis['in_progress_wo'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breakdown -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background: linear-gradient(135deg, #f44336 0%, #b71c1c 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">Breakdown</span>
                        <i class="ki-outline ki-shield-cross fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $kpis['breakdown_wo'] }}</div>
                    <div class="text-white opacity-75 fs-9">Perbaikan Darurat Aktif</div>
                </div>
            </div>
        </div>

        <!-- Waiting Part -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background: linear-gradient(135deg, #0288d1 0%, #01579b 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">Waiting Part</span>
                        <i class="ki-outline ki-package fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $kpis['waiting_spareparts'] }}</div>
                    <div class="text-white opacity-75 fs-9">Menunggu Kedatangan Part</div>
                </div>
            </div>
        </div>

        <!-- MOL SCM -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background: linear-gradient(135deg, #388e3c 0%, #1b5e20 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">MOL SCM</span>
                        <i class="ki-outline ki-handcart fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $kpis['mol_requests'] }}</div>
                    <div class="text-white opacity-75 fs-9">Order Part Aktif di SCM</div>
                </div>
            </div>
        </div>

        <!-- WO Selesai -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background: linear-gradient(135deg, #455a64 0%, #263238 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">WO Selesai</span>
                        <i class="ki-outline ki-verify fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $kpis['completed_wo'] }}</div>
                    <div class="text-white opacity-75 fs-9">Completed & Closed</div>
                </div>
            </div>
        </div>
    </div>
    <!--end::KPI Stats Row-->

    <!--begin::Quick Action Banners-->
    <div class="row g-4 mb-6">
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('plt.workorder') }}" class="card card-flush shadow-xs border border-gray-200 h-100 bg-white text-hover-primary p-4 d-flex flex-row align-items-center gap-3 text-decoration-none">
                <div class="symbol symbol-45px symbol-circle bg-light-primary">
                    <i class="ki-outline ki-wrench fs-2 text-primary"></i>
                </div>
                <div>
                    <div class="fw-bolder text-gray-900 fs-6">Kelola Work Order</div>
                    <span class="fs-9 text-muted">Multi-Problem & Action Hierarki</span>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('scm.mol') }}" class="card card-flush shadow-xs border border-gray-200 h-100 bg-white text-hover-success p-4 d-flex flex-row align-items-center gap-3 text-decoration-none">
                <div class="symbol symbol-45px symbol-circle bg-light-success">
                    <i class="ki-outline ki-handcart fs-2 text-success"></i>
                </div>
                <div>
                    <div class="fw-bolder text-gray-900 fs-6">Request Sparepart (MOL)</div>
                    <span class="fs-9 text-muted">Cek Stok & Auto-Generate PR</span>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('plt.components') }}" class="card card-flush shadow-xs border border-gray-200 h-100 bg-white text-hover-info p-4 d-flex flex-row align-items-center gap-3 text-decoration-none">
                <div class="symbol symbol-45px symbol-circle bg-light-info">
                    <i class="ki-outline ki-cube-2 fs-2 text-info"></i>
                </div>
                <div>
                    <div class="fw-bolder text-gray-900 fs-6">Component Tracker</div>
                    <span class="fs-9 text-muted">Silsilah, Rotasi & Life Meter</span>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6">
            <a href="{{ route('plt.far') }}" class="card card-flush shadow-xs border border-gray-200 h-100 bg-white text-hover-danger p-4 d-flex flex-row align-items-center gap-3 text-decoration-none">
                <div class="symbol symbol-45px symbol-circle bg-light-danger">
                    <i class="ki-outline ki-shield-cross fs-2 text-danger"></i>
                </div>
                <div>
                    <div class="fw-bolder text-gray-900 fs-6">Investigasi Kerusakan (FAR)</div>
                    <span class="fs-9 text-muted">Root Cause 5-Why & CAPA</span>
                </div>
            </a>
        </div>
    </div>
    <!--end::Quick Action Banners-->

    <!--begin::Main Content Row-->
    <div class="row g-5 mb-6">

        <!--begin::Recent WO Table (left 8 cols)-->
        <div class="col-xl-8">
            <div class="card card-flush shadow-sm border border-gray-200 h-100 bg-white">
                <div class="card-header pt-5 pb-3 border-bottom-0">
                    <h3 class="card-title fw-bolder text-gray-900 fs-5">
                        <i class="ki-outline ki-wrench fs-3 text-primary me-2"></i> Aktivitas Work Order Terkini
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
                                    <th>No. WO & Tanggal</th>
                                    <th>Unit & Lokasi</th>
                                    <th>Pekerjaan / Problem</th>
                                    <th>Tipe & Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentWorkOrders as $wo)
                                    <tr>
                                        <td>
                                            <a href="{{ route('plt.workorder') }}" class="text-gray-900 fw-bold text-hover-primary fs-7">
                                                {{ $wo->wo_number }}
                                            </a>
                                            <span class="text-muted fs-9 d-block">{{ $wo->wo_date->format('d M Y') }}</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-900 fw-bold fs-7">{{ $wo->equipment->unit ?? 'Unit' }}</span>
                                            <span class="text-muted fs-9 d-block">{{ $wo->equipment->reffEquip?->model ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-semibold d-block text-truncate fs-8" style="max-width: 200px;" title="{{ $wo->job_title }}">
                                                {{ $wo->job_title }}
                                            </span>
                                            <span class="badge badge-light-secondary fs-9">{{ $wo->tasks->count() }} Problem Log</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-{{ $wo->wo_type === 'breakdown' ? 'danger' : ($wo->wo_type === 'preventive' ? 'info' : ($wo->wo_type === 'corrective' ? 'warning' : 'primary')) }} fs-9 fw-bold d-block mb-1">
                                                {{ $wo->type_badge['label'] }}
                                            </span>
                                            <span class="badge badge-light-{{ $wo->status === 'open' ? 'warning' : ($wo->status === 'in_progress' ? 'primary' : ($wo->status === 'completed' ? 'success' : 'dark')) }} fs-9 fw-bold">
                                                {{ $wo->status_badge['label'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-8">
                                            <i class="ki-outline ki-document fs-3x text-gray-300 mb-3 d-block"></i>
                                            <span class="text-muted fs-8">Belum ada aktivitas Work Order.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Recent WO Table-->

        <!--begin::WO Breakdown by Type + Status (right 4 cols)-->
        <div class="col-xl-4">
            <div class="card card-flush shadow-sm border border-gray-200 bg-white mb-5">
                <div class="card-header pt-5 pb-3 border-bottom-0">
                    <h3 class="card-title fw-bolder text-gray-900 fs-6">
                        <i class="ki-outline ki-chart-pie-4 fs-4 text-primary me-2"></i> WO berdasarkan Tipe
                    </h3>
                </div>
                <div class="card-body pt-0 pb-4">
                    @php
                        $typeLabels = [
                            'breakdown' => ['label' => 'Breakdown (BD)', 'class' => 'bg-danger'],
                            'corrective' => ['label' => 'Corrective (CM)', 'class' => 'bg-warning'],
                            'preventive' => ['label' => 'Preventive (PM)', 'class' => 'bg-info'],
                            'plan' => ['label' => 'Plan (PM Plan)', 'class' => 'bg-primary'],
                            'inspection' => ['label' => 'Inspection', 'class' => 'bg-success'],
                            'overhaul' => ['label' => 'Overhaul', 'class' => 'bg-dark'],
                        ];
                        $totalWo = $woByType->sum() ?: 1;
                    @endphp
                    <div class="d-flex flex-column gap-3">
                        @foreach($woByType as $type => $count)
                            @php $meta = $typeLabels[$type] ?? ['label' => ucfirst($type), 'class' => 'bg-secondary']; @endphp
                            <div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-semibold text-gray-700 fs-8">{{ $meta['label'] }}</span>
                                    <span class="fw-bolder text-gray-900 fs-8">{{ $count }}</span>
                                </div>
                                <div class="progress h-6px rounded-3">
                                    <div class="progress-bar {{ $meta['class'] }} rounded-3" style="width: {{ round($count / $totalWo * 100) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!--begin::Top Downtime-->
            <div class="card card-flush shadow-sm border border-gray-200 bg-white">
                <div class="card-header pt-5 pb-3 border-bottom-0">
                    <h3 class="card-title fw-bolder text-gray-900 fs-6">
                        <i class="ki-outline ki-time fs-4 text-danger me-2"></i> Top Downtime (Jam)
                    </h3>
                </div>
                <div class="card-body pt-0 pb-4">
                    @if($topDowntime->count() > 0)
                        <div class="d-flex flex-column gap-3">
                            @foreach($topDowntime as $wo)
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-gray-900 fs-8">{{ $wo->equipment->unit ?? 'N/A' }}</span>
                                        <span class="text-muted fs-9">{{ $wo->wo_number }}</span>
                                    </div>
                                    <span class="badge badge-light-danger fw-bolder fs-8">{{ number_format($wo->downtime_hours, 1) }} Jam</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted fs-8 text-center py-3">Tidak ada data downtime.</p>
                    @endif
                </div>
            </div>
            <!--end::Top Downtime-->
        </div>
        <!--end::Right Column-->
    </div>
    <!--end::Main Content Row-->

    <!--begin::Second Row: Equipment Fleet + Discussion Activity-->
    <div class="row g-5 mb-6">

        <!--begin::Equipment Fleet-->
        <div class="col-xl-5">
            <div class="card card-flush shadow-sm border border-gray-200 h-100 bg-white">
                <div class="card-header pt-5 pb-3 border-bottom-0">
                    <h3 class="card-title fw-bolder text-gray-900 fs-5">
                        <i class="ki-outline ki-truck fs-3 text-success me-2"></i> Populasi Unit Alat Berat
                    </h3>
                    <div class="card-toolbar">
                    <a href="/admin/equipment" class="btn btn-sm btn-light-success fw-bold fs-8">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 fs-7">
                            <thead>
                                <tr class="fw-bold text-muted fs-8 text-uppercase">
                                    <th>Unit</th>
                                    <th>Model & Kategori</th>
                                    <th>Site</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($equipmentOverview as $eq)
                                    <tr>
                                        <td>
                                            <span class="badge badge-light-dark fs-8 fw-bolder">{{ $eq->unit }}</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-semibold fs-8 d-block">{{ $eq->reffEquip?->model ?? 'Model' }}</span>
                                            <span class="text-muted fs-9">{{ $eq->reffEquip?->category ?? '-' }}</span>
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
        <!--end::Equipment Fleet-->

        <!--begin::Plant Engineering Module Links-->
        <div class="col-xl-3">
            <div class="card card-flush shadow-sm border border-gray-200 h-100 bg-white">
                <div class="card-header pt-5 pb-3 border-bottom-0">
                    <h3 class="card-title fw-bolder text-gray-900 fs-5">
                        <i class="ki-outline ki-abstract-26 fs-3 text-warning me-2"></i> Modul Plant Engineering
                    </h3>
                </div>
                <div class="card-body pt-0 pb-3">
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('plt.components') }}" class="d-flex align-items-center p-3 rounded-2 bg-light-primary text-hover-primary text-decoration-none gap-3">
                            <i class="ki-outline ki-cube-2 fs-3 text-primary"></i>
                            <div>
                                <div class="fw-bolder text-gray-900 fs-7">Component Tracker</div>
                                <span class="text-muted fs-9">Silsilah, Rotasi & Life Meter</span>
                            </div>
                        </a>
                        <a href="{{ route('plt.ccr') }}" class="d-flex align-items-center p-3 rounded-2 bg-light-info text-hover-info text-decoration-none gap-3">
                            <i class="ki-outline ki-clipboard fs-3 text-info"></i>
                            <div>
                                <div class="fw-bolder text-gray-900 fs-7">Kondisi Fisik (CCR)</div>
                                <span class="text-muted fs-9">Evaluasi Keausan & Sisa Umur</span>
                            </div>
                        </a>
                        <a href="{{ route('plt.far') }}" class="d-flex align-items-center p-3 rounded-2 bg-light-danger text-hover-danger text-decoration-none gap-3">
                            <i class="ki-outline ki-shield-cross fs-3 text-danger"></i>
                            <div>
                                <div class="fw-bolder text-gray-900 fs-7">Investigasi Kerusakan (FAR)</div>
                                <span class="text-muted fs-9">Root Cause 5-Why & CAPA</span>
                            </div>
                        </a>
                        <a href="{{ route('plt.osr') }}" class="d-flex align-items-center p-3 rounded-2 bg-light-success text-hover-success text-decoration-none gap-3">
                            <i class="ki-outline ki-delivery-3 fs-3 text-success"></i>
                            <div>
                                <div class="fw-bolder text-gray-900 fs-7">Perbaikan Luar (OSR)</div>
                                <span class="text-muted fs-9">Vendor Bubut & QC Garansi</span>
                            </div>
                        </a>
                        <a href="{{ route('chat') }}" class="d-flex align-items-center p-3 rounded-2 bg-light-warning text-hover-warning text-decoration-none gap-3">
                            <i class="ki-outline ki-messages fs-3 text-warning"></i>
                            <div>
                                <div class="fw-bolder text-gray-900 fs-7">Messenger & Group</div>
                                <span class="text-muted fs-9">Koordinasi Mekanik & Logistik</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Plant Engineering Module Links-->

        <!--begin::Recent WO Discussion Activity-->
        <div class="col-xl-4">
            <div class="card card-flush shadow-sm border border-gray-200 h-100 bg-white">
                <div class="card-header pt-5 pb-3 border-bottom-0">
                    <h3 class="card-title fw-bolder text-gray-900 fs-5">
                        <i class="ki-outline ki-message-text-2 fs-3 text-primary me-2"></i> Diskusi WO Terbaru
                    </h3>
                    <div class="card-toolbar">
                        <a href="{{ route('plt.workorder') }}" class="btn btn-sm btn-light-primary fw-bold fs-8">Buka WO</a>
                    </div>
                </div>
                <div class="card-body pt-0 pb-4">
                    @if($recentComments->count() > 0)
                        <div class="d-flex flex-column gap-4">
                            @foreach($recentComments as $comment)
                                <div class="d-flex align-items-start gap-3">
                                    <div class="symbol symbol-32px symbol-circle flex-shrink-0">
                                        <div class="symbol-label fs-9 fw-bolder bg-primary text-white">
                                            {{ $comment->initials }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="d-flex align-items-center justify-content-between gap-2">
                                            <span class="fw-bolder text-gray-900 fs-8">{{ $comment->user?->full_name ?? 'User' }}</span>
                                            <span class="text-muted fs-9 text-nowrap">{{ $comment->time_ago }}</span>
                                        </div>
                                        <span class="badge badge-light-primary fs-9 mb-1">{{ $comment->workOrder?->wo_number ?? 'WO' }}</span>
                                        <p class="text-gray-700 fs-8 mb-0 text-truncate" style="max-width: 280px;" title="{{ $comment->body }}">
                                            {{ $comment->body }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6">
                            <i class="ki-outline ki-message-text-2 fs-3x text-gray-300 mb-2 d-block"></i>
                            <span class="text-muted fs-8">Belum ada diskusi Work Order.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!--end::Recent WO Discussion Activity-->
    </div>
    <!--end::Second Row-->

</div>
