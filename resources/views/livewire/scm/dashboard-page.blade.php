<div>
    <!--begin::Page Header-->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-6 gap-3">
        <div>
            <h1 class="fs-2hx fw-bolder text-gray-900 mb-1">
                <i class="ki-outline ki-shop fs-1 text-success me-2"></i> Supply Chain & Logistics Center (SCM)
            </h1>
            <span class="fs-7 text-muted fw-semibold">Pusat kendali pengadaan, material order mekanik, evaluasi penawaran vendor, pengiriman DO, dan manajemen stok gudang.</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('scm.mol') }}" class="btn btn-sm btn-primary fw-bold">
                <i class="ki-outline ki-plus fs-4 me-1"></i> Buat Request MOL
            </a>
            <a href="{{ route('scm.pr') }}" class="btn btn-sm btn-light-primary fw-bold">
                <i class="ki-outline ki-document fs-4 me-1"></i> Buat PR Baru
            </a>
        </div>
    </div>
    <!--end::Page Header-->

    <!--begin::KPI Stats Row-->
    <div class="row g-5 g-xl-8 mb-6">
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4">
                    <span class="badge badge-light-warning text-warning fw-bold fs-8 mb-2">MOL Pending</span>
                    <div class="fs-2hx fw-bolder text-gray-900">{{ $metrics['mol_pending'] }}</div>
                    <div class="fs-9 text-muted">Request Part Mekanik</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4">
                    <span class="badge badge-light-primary text-primary fw-bold fs-8 mb-2">PR Menunggu</span>
                    <div class="fs-2hx fw-bolder text-primary">{{ $metrics['pr_pending'] }}</div>
                    <div class="fs-9 text-muted">Purchase Request</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4">
                    <span class="badge badge-light-info text-info fw-bold fs-8 mb-2">PO Aktif</span>
                    <div class="fs-2hx fw-bolder text-info">{{ $metrics['po_active'] }}</div>
                    <div class="fs-9 text-muted">Pesanan ke Vendor</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4">
                    <span class="badge badge-light-warning text-warning fw-bold fs-8 mb-2">DO In Transit</span>
                    <div class="fs-2hx fw-bolder text-warning">{{ $metrics['do_in_transit'] }}</div>
                    <div class="fs-9 text-muted">Pengiriman to Site</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4">
                    <span class="badge badge-light-danger text-danger fw-bold fs-8 mb-2">Stok Kritis</span>
                    <div class="fs-2hx fw-bolder text-danger">{{ $metrics['critical_stock'] }}</div>
                    <div class="fs-9 text-muted">&le; Minimum Stock</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4">
                    <span class="badge badge-light-success text-success fw-bold fs-8 mb-2">Total PO IDR</span>
                    <div class="fs-5 fw-bolder text-success text-truncate" title="Rp {{ number_format($metrics['total_po_value'], 0, ',', '.') }}">
                        Rp {{ number_format($metrics['total_po_value'] / 1000000, 1) }}M
                    </div>
                    <div class="fs-9 text-muted">Nilai Pengadaan</div>
                </div>
            </div>
        </div>
    </div>
    <!--end::KPI Stats Row-->

    <!--begin::Row 2: Recent MOL & Critical Stock-->
    <div class="row g-5 g-xl-8 mb-6">
        <!-- Recent MOL Request -->
        <div class="col-xl-7">
            <div class="card card-flush shadow-sm border border-gray-200 h-100 bg-white">
                <div class="card-header pt-5 pb-3">
                    <h3 class="card-title fw-bolder text-gray-900 fs-5">
                        <i class="ki-outline ki-handcart fs-3 text-primary me-2"></i> Request Part Terkini (MOL)
                    </h3>
                    <div class="card-toolbar">
                        <a href="{{ route('scm.mol') }}" class="btn btn-sm btn-light-primary fw-bold fs-8">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 fs-7">
                            <thead>
                                <tr class="fw-bold text-muted fs-8 text-uppercase">
                                    <th>No. MOL</th>
                                    <th>Unit WO</th>
                                    <th>Item Diminta</th>
                                    <th>Pemohon</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentMols as $mol)
                                    <tr>
                                        <td>
                                            <a href="{{ route('scm.mol') }}" class="text-gray-900 fw-bold text-hover-primary">
                                                {{ $mol->mol_number }}
                                            </a>
                                            <span class="text-muted fs-9 d-block">{{ $mol->mol_date->format('d/m/Y') }}</span>
                                        </td>
                                        <td>
                                            <span class="text-gray-800 fw-semibold">{{ $mol->workOrder->equipment->unit ?? 'General' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-secondary fs-8">{{ $mol->items->count() }} Items</span>
                                        </td>
                                        <td>{{ $mol->requester->full_name ?? 'User' }}</td>
                                        <td>
                                            <span class="badge {{ $mol->status_badge['class'] }} fs-8 fw-bold">
                                                {{ $mol->status_badge['label'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-6 text-muted fs-8">Belum ada data request MOL.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Critical Stock Monitor -->
        <div class="col-xl-5">
            <div class="card card-flush shadow-sm border border-gray-200 h-100 bg-white">
                <div class="card-header pt-5 pb-3">
                    <h3 class="card-title fw-bolder text-gray-900 fs-5">
                        <i class="ki-outline ki-shield-cross fs-3 text-danger me-2"></i> Peringatan Stok Minimum Gudang
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 fs-7">
                            <thead>
                                <tr class="fw-bold text-muted fs-8 text-uppercase">
                                    <th>Part Number</th>
                                    <th>Nama Suku Cadang</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStocks as $part)
                                    <tr>
                                        <td class="fw-bold text-gray-900">{{ $part->part_number }}</td>
                                        <td>
                                            <span class="text-gray-800 text-truncate d-inline-block" style="max-width: 140px;" title="{{ $part->name }}">
                                                {{ $part->name }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold {{ $part->stock_on_hand <= 0 ? 'text-danger' : 'text-warning' }}">
                                                {{ $part->stock_on_hand }} {{ $part->uom }}
                                            </span>
                                            <span class="text-muted fs-9 d-block">(Min: {{ $part->min_stock }})</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $part->stock_badge['class'] }} fs-9">
                                                {{ $part->stock_badge['label'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-6 text-success fs-8">
                                            <i class="ki-outline ki-check-circle fs-2 me-1"></i> Semua stok aman di atas batas minimum.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Row 2-->

    <!--begin::Row 3: SCM Flow Visual Guide-->
    <div class="card card-flush shadow-sm border border-gray-200 bg-white">
        <div class="card-header pt-5 pb-3">
            <h3 class="card-title fw-bolder text-gray-900 fs-5">
                <i class="ki-outline ki-arrows-loop fs-3 text-primary me-2"></i> Alur Terpadu Enterprise SCM & Logistik
            </h3>
        </div>
        <div class="card-body pt-0 pb-5">
            <div class="row g-4 text-center">
                <div class="col-md-2 col-sm-4">
                    <a href="{{ route('scm.mol') }}" class="d-block p-4 border rounded-3 text-hover-primary bg-light bg-opacity-50">
                        <div class="symbol symbol-45px symbol-circle bg-light-primary text-primary mx-auto mb-2">
                            <i class="ki-outline ki-handcart fs-2"></i>
                        </div>
                        <div class="fw-bold fs-7 text-gray-900">1. MOL Request</div>
                        <span class="fs-9 text-muted">Permintaan dari Mekanik & WO</span>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4">
                    <a href="{{ route('scm.pr') }}" class="d-block p-4 border rounded-3 text-hover-primary bg-light bg-opacity-50">
                        <div class="symbol symbol-45px symbol-circle bg-light-warning text-warning mx-auto mb-2">
                            <i class="ki-outline ki-document fs-2"></i>
                        </div>
                        <div class="fw-bold fs-7 text-gray-900">2. PR (Purchase)</div>
                        <span class="fs-9 text-muted">Jika stok kosong / pengadaan</span>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4">
                    <a href="{{ route('scm.rfq') }}" class="d-block p-4 border rounded-3 text-hover-primary bg-light bg-opacity-50">
                        <div class="symbol symbol-45px symbol-circle bg-light-info text-info mx-auto mb-2">
                            <i class="ki-outline ki-calculator fs-2"></i>
                        </div>
                        <div class="fw-bold fs-7 text-gray-900">3. RFQ & Vendor</div>
                        <span class="fs-9 text-muted">Bandingkan harga, diskon & PPN</span>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4">
                    <a href="{{ route('scm.po') }}" class="d-block p-4 border rounded-3 text-hover-primary bg-light bg-opacity-50">
                        <div class="symbol symbol-45px symbol-circle bg-light-primary text-primary mx-auto mb-2">
                            <i class="ki-outline ki-notepad-edit fs-2"></i>
                        </div>
                        <div class="fw-bold fs-7 text-gray-900">4. Purchase Order</div>
                        <span class="fs-9 text-muted">Approval PO resmi ke Vendor</span>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4">
                    <a href="{{ route('scm.do') }}" class="d-block p-4 border rounded-3 text-hover-primary bg-light bg-opacity-50">
                        <div class="symbol symbol-45px symbol-circle bg-light-warning text-warning mx-auto mb-2">
                            <i class="ki-outline ki-delivery-3 fs-2"></i>
                        </div>
                        <div class="fw-bold fs-7 text-gray-900">5. DO Delivery</div>
                        <span class="fs-9 text-muted">Pengiriman barang to HO/Site</span>
                    </a>
                </div>
                <div class="col-md-2 col-sm-4">
                    <a href="{{ route('scm.gr') }}" class="d-block p-4 border rounded-3 text-hover-primary bg-light bg-opacity-50">
                        <div class="symbol symbol-45px symbol-circle bg-light-success text-success mx-auto mb-2">
                            <i class="ki-outline ki-package fs-2"></i>
                        </div>
                        <div class="fw-bold fs-7 text-gray-900">6. Goods Receipt</div>
                        <span class="fs-9 text-muted">Terima fisik & tambah stok</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!--end::Row 3-->
</div>
