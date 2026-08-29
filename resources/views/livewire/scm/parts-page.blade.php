<div>
    <!--begin::Toolbar-->
    <div class="d-flex flex-wrap flex-stack pb-6">
        <div>
            <h1 class="text-gray-900 fw-bolder fs-2 mb-1">
                <i class="ki-outline ki-cube-2 text-primary fs-1 me-2"></i> Master Spareparts & Suku Cadang
            </h1>
            <div class="text-muted fs-7">Katalog data suku cadang logistik, ketersediaan stok fisik gudang, dan valuasi aset inventaris.</div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('scm.mol') }}" class="btn btn-sm btn-light-primary fw-bold">
                <i class="ki-outline ki-handcart fs-4 me-1"></i> Lihat Permintaan MOL
            </a>
            @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('admin'))
                <a href="{{ url('/admin/parts') }}" class="btn btn-sm btn-primary fw-bold">
                    <i class="ki-outline ki-plus fs-4 me-1"></i> Kelola Master Part (Admin)
                </a>
            @endif
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Stats KPI Row-->
    <div class="row g-4 mb-6">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-bordered h-100 shadow-xs border-0 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle bg-light-primary text-primary me-3 d-flex align-items-center justify-content-center">
                        <i class="ki-outline ki-cube-2 fs-2 text-primary"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-gray-900">{{ number_format($metrics['total_items']) }} Part</div>
                        <div class="fs-8 text-muted">Total SKU Terdaftar</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-bordered h-100 shadow-xs border-0 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle bg-light-success text-success me-3 d-flex align-items-center justify-content-center">
                        <i class="ki-outline ki-delivery-3 fs-2 text-success"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-gray-900">{{ number_format($metrics['total_stock_on_hand']) }} Unit</div>
                        <div class="fs-8 text-muted">Total Stok Fisik Gudang</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-bordered h-100 shadow-xs border-0 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle bg-light-warning text-warning me-3 d-flex align-items-center justify-content-center">
                        <i class="ki-outline ki-information-4 fs-2 text-warning"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-warning">{{ number_format($metrics['low_stock']) }} SKU</div>
                        <div class="fs-8 text-muted">Stok Minimum (Reorder)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-bordered h-100 shadow-xs border-0 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle bg-light-danger text-danger me-3 d-flex align-items-center justify-content-center">
                        <i class="ki-outline ki-cross-circle fs-2 text-danger"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-danger">{{ number_format($metrics['out_of_stock']) }} SKU</div>
                        <div class="fs-8 text-muted">Stok Kosong / Habis</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Stats KPI Row-->

    <!--begin::Filter & Search Bar-->
    <div class="card card-bordered shadow-xs border-0 bg-white mb-6">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <div class="position-relative">
                        <i class="ki-outline ki-magnifier fs-3 position-absolute top-50 translate-middle-y ms-4 text-gray-500"></i>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control form-control-solid ps-12 fs-7"
                            placeholder="Cari Part Number, Nama Part, Kategori, atau Bin Lokasi..."
                        />
                    </div>
                </div>

                <div class="col-md-3">
                    <select wire:model.live="filterCategory" class="form-select form-select-solid fs-7">
                        <option value="all">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <select wire:model.live="filterStockStatus" class="form-select form-select-solid fs-7">
                        <option value="all">Semua Status Stok</option>
                        <option value="ready">Stok Aman (Ready)</option>
                        <option value="low_stock">Stok Menipis (Low Stock)</option>
                        <option value="out_of_stock">Stok Habis (Out of Stock)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <!--end::Filter & Search Bar-->

    <!--begin::Table Data Master Parts-->
    <div class="card card-bordered shadow-xs border-0 bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-gray-200 align-middle gs-4 gy-4 mb-0">
                    <thead class="bg-light fw-bold fs-7 text-gray-700 text-uppercase">
                        <tr>
                            <th class="ps-4">Part Number & Nama</th>
                            <th>Kategori & Penempatan Rak</th>
                            <th>Stok Fisik / On Hand</th>
                            <th>Batas Min / Max</th>
                            <th>Harga Standar (Rp)</th>
                            <th>Status Ketersediaan</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="fs-7">
                        @forelse($parts as $part)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40px symbol-rounded bg-light-primary text-primary me-3 d-flex align-items-center justify-content-center">
                                            <i class="ki-outline ki-cube-2 fs-3 text-primary"></i>
                                        </div>
                                        <div>
                                            <span class="fw-bolder text-gray-900 fs-6 d-block">{{ $part->part_number }}</span>
                                            <span class="text-muted fs-8">{{ $part->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <span class="badge badge-light fw-bold text-gray-800 w-fit px-2 py-0.5 fs-8">
                                            {{ $part->category ?? 'Umum' }}
                                        </span>
                                        
                                        @if($part->locations && $part->locations->count() > 0)
                                            <div class="d-flex flex-column gap-1 mt-1">
                                                @foreach($part->locations as $loc)
                                                    <span class="badge badge-light-primary fs-9 py-0.5 px-2 text-start text-truncate" style="max-width: 250px;" title="{{ $loc->site->site_name ?? 'Gudang' }} - {{ $loc->warehouse_name }}: {{ $loc->notes }}">
                                                        <i class="ki-outline ki-geolocation fs-9 me-1 text-primary"></i>
                                                        <strong>{{ $loc->site->site_name ?? 'Gudang' }}:</strong> {{ $loc->rack_location }} 
                                                        @if($loc->stock_qty > 0)
                                                            <span class="text-muted">({{ number_format($loc->stock_qty, 0) }} {{ $part->uom }})</span>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted fs-8">
                                                <i class="ki-outline ki-geolocation fs-8 me-1 text-gray-500"></i> {{ $part->bin_location ?? 'Rak A-01' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-baseline">
                                        <span class="fs-5 fw-bolder {{ $part->stock_on_hand <= 0 ? 'text-danger' : ($part->stock_on_hand <= $part->min_stock ? 'text-warning' : 'text-gray-900') }}">
                                            {{ number_format($part->stock_on_hand, 0) }}
                                        </span>
                                        <span class="text-muted fs-8 ms-1">{{ $part->uom ?? 'Pcs' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted fs-8">
                                        <div>Min: <span class="fw-semibold text-gray-700">{{ number_format($part->min_stock, 0) }}</span></div>
                                        <div>Max: <span class="fw-semibold text-gray-700">{{ number_format($part->max_stock, 0) }}</span></div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-gray-800">
                                        Rp {{ number_format($part->standard_cost, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    @if($part->stock_on_hand <= 0)
                                        <span class="badge badge-light-danger fw-bold fs-8">
                                            <i class="ki-outline ki-cross-circle fs-8 text-danger me-1"></i> Habis
                                        </span>
                                    @elseif($part->stock_on_hand <= $part->min_stock)
                                        <span class="badge badge-light-warning fw-bold fs-8">
                                            <i class="ki-outline ki-information-4 fs-8 text-warning me-1"></i> Menipis
                                        </span>
                                    @else
                                        <span class="badge badge-light-success fw-bold fs-8">
                                            <i class="ki-outline ki-check-circle fs-8 text-success me-1"></i> Tersedia
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <button
                                        type="button"
                                        wire:click="openDetail('{{ $part->id }}')"
                                        class="btn btn-sm btn-icon btn-light-primary"
                                        title="Lihat Detail Part & Multi-Rak"
                                    >
                                        <i class="ki-outline ki-eye fs-4"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-10">
                                    <i class="ki-outline ki-cube-2 fs-3x text-muted mb-2"></i>
                                    <div class="fs-6 fw-bold text-gray-600">Tidak ada sparepart ditemukan</div>
                                    <div class="fs-8 text-muted">Coba ubah kata kunci pencarian atau filter kategori.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($parts->hasPages())
                <div class="p-4 border-top">
                    {{ $parts->links() }}
                </div>
            @endif
        </div>
    </div>
    <!--end::Table Data Master Parts-->

    <!--begin::Modal Detail Part-->
    @if($showDetailModal && $selectedPart)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered mw-750px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-4 px-6 bg-light">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px symbol-circle bg-light-primary text-primary me-3 d-flex align-items-center justify-content-center">
                                <i class="ki-outline ki-cube-2 fs-3 text-primary"></i>
                            </div>
                            <div>
                                <h4 class="modal-title fw-bolder text-gray-900">{{ $selectedPart->part_number }}</h4>
                                <span class="text-muted fs-8">{{ $selectedPart->name }}</span>
                            </div>
                        </div>
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <div class="modal-body p-6" style="max-height: 75vh; overflow-y: auto;">
                        <div class="row g-3 mb-4">
                            <div class="col-sm-4">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="fs-8 text-muted">Kategori</div>
                                    <div class="fs-7 fw-bold text-gray-900">{{ $selectedPart->category ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="p-3 bg-light-success rounded-3">
                                    <div class="fs-8 text-muted">Total Stok Fisik</div>
                                    <div class="fs-5 fw-bolder text-success">{{ number_format($selectedPart->stock_on_hand, 0) }} {{ $selectedPart->uom }}</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="fs-8 text-muted">Harga Standar / Unit</div>
                                    <div class="fs-6 fw-bold text-gray-900">Rp {{ number_format($selectedPart->standard_cost, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Lokasi Penempatan Multi-Site & Multi-Rak -->
                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h5 class="fs-7 fw-bold text-gray-900 mb-0">
                                    <i class="ki-outline ki-geolocation fs-5 text-primary me-1"></i> Penempatan Rak & Lokasi Gudang (Multi-Site)
                                </h5>
                                <span class="badge badge-light-primary fs-9 fw-bold">{{ $selectedPart->locations ? $selectedPart->locations->count() : 0 }} Lokasi Terdaftar</span>
                            </div>

                            @if($selectedPart->locations && $selectedPart->locations->count() > 0)
                                <div class="table-responsive border rounded-3">
                                    <table class="table table-bordered table-sm gs-3 gy-2 fs-8 bg-white mb-0 align-middle">
                                        <thead class="bg-light fw-bold text-gray-700 text-uppercase">
                                            <tr>
                                                <th>Site Operasional</th>
                                                <th>Nama Gudang / Workshop</th>
                                                <th>Kode Rak / Bin</th>
                                                <th class="text-center" style="width: 80px;">Stok</th>
                                                <th class="text-center" style="width: 90px;">Tipe</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($selectedPart->locations as $loc)
                                                <tr>
                                                    <td>
                                                        <span class="fw-bold text-gray-900">{{ $loc->site->site_name ?? 'Gudang Pusat' }}</span>
                                                    </td>
                                                    <td>{{ $loc->warehouse_name }}</td>
                                                    <td>
                                                        <span class="badge badge-light-info fw-bolder fs-8">
                                                            <i class="ki-outline ki-cube-2 fs-9 me-1"></i> {{ $loc->rack_location }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center fw-bold">{{ number_format($loc->stock_qty, 0) }} {{ $selectedPart->uom }}</td>
                                                    <td class="text-center">
                                                        @if($loc->is_primary)
                                                            <span class="badge badge-light-success fs-9 fw-bold">Utama</span>
                                                        @else
                                                            <span class="badge badge-light-secondary fs-9">Buffer</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-muted fs-9">{{ $loc->notes ?: '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-3 bg-light rounded-3 text-muted fs-8">
                                    Belum ada rincian multi-rak. Lokasi default: <strong>{{ $selectedPart->bin_location ?? 'Rak A-01' }}</strong>
                                </div>
                            @endif
                        </div>

                        @if($selectedPart->description)
                            <div class="mb-4">
                                <div class="fs-8 text-muted mb-1">Deskripsi & Spesifikasi:</div>
                                <div class="p-3 bg-light rounded-3 fs-8 text-gray-800">{{ $selectedPart->description }}</div>
                            </div>
                        @endif

                        <div class="d-flex align-items-center justify-content-between p-3 bg-light-primary rounded-3">
                            <div class="fs-8 text-primary fw-bold">Total Nilai Aset Stok:</div>
                            <div class="fs-6 fw-bolder text-primary">Rp {{ number_format($selectedPart->stock_on_hand * $selectedPart->standard_cost, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <div class="modal-footer border-top py-3 px-6">
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-light fs-7">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Detail Part-->
</div>
