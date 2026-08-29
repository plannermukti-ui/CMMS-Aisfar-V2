<div>
    <!--begin::Toolbar-->
    <div class="d-flex flex-wrap flex-stack pb-6">
        <div>
            <h1 class="text-gray-900 fw-bolder fs-2 mb-1">
                <i class="ki-outline ki-clipboard text-primary fs-1 me-2"></i> Stock Opname & Penyesuaian Stok
            </h1>
            <div class="text-muted fs-7">Pencocokan fisik suku cadang gudang, verifikasi selisih stok (variance), approval manajerial, dan Berita Acara resmi.</div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" wire:click="openCreateModal" class="btn btn-sm btn-primary fw-bold">
                <i class="ki-outline ki-plus-circle fs-4 me-1"></i> Mulai Sesi Stock Opname
            </button>
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Stats KPI Row-->
    <div class="row g-4 mb-6">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-bordered h-100 shadow-xs border-0 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle bg-light-primary text-primary me-3 d-flex align-items-center justify-content-center">
                        <i class="ki-outline ki-clipboard fs-2 text-primary"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-gray-900">{{ number_format($metrics['total']) }} Sesi</div>
                        <div class="fs-8 text-muted">Total Riwayat Opname</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-bordered h-100 shadow-xs border-0 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle bg-light-warning text-warning me-3 d-flex align-items-center justify-content-center">
                        <i class="ki-outline ki-time fs-2 text-warning"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-warning">{{ number_format($metrics['submitted']) }} Sesi</div>
                        <div class="fs-8 text-muted">Menunggu Approval SCM</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-bordered h-100 shadow-xs border-0 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle bg-light-success text-success me-3 d-flex align-items-center justify-content-center">
                        <i class="ki-outline ki-check-circle fs-2 text-success"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-success">{{ number_format($metrics['approved']) }} Sesi</div>
                        <div class="fs-8 text-muted">Disetujui & Stok Sinkron</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-bordered h-100 shadow-xs border-0 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle bg-light-danger text-danger me-3 d-flex align-items-center justify-content-center">
                        <i class="ki-outline ki-bill fs-2 text-danger"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-danger">Rp {{ number_format($metrics['total_variance_value'], 0, ',', '.') }}</div>
                        <div class="fs-8 text-muted">Total Nilai Variansi Selisih</div>
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
                <div class="col-md-8">
                    <div class="position-relative">
                        <i class="ki-outline ki-magnifier fs-3 position-absolute top-50 translate-middle-y ms-4 text-gray-500"></i>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control form-control-solid ps-12 fs-7"
                            placeholder="Cari Nomor Opname, Nomor Berita Acara, Petugas, atau Catatan..."
                        />
                    </div>
                </div>

                <div class="col-md-4">
                    <select wire:model.live="filterStatus" class="form-select form-select-solid fs-7">
                        <option value="all">Semua Status Opname</option>
                        <option value="submitted">Menunggu Approval</option>
                        <option value="approved">Approved & Disesuaikan</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <!--end::Filter & Search Bar-->

    <!--begin::Table Opname List-->
    <div class="card card-bordered shadow-xs border-0 bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-gray-200 align-middle gs-4 gy-4 mb-0">
                    <thead class="bg-light fw-bold fs-7 text-gray-700 text-uppercase">
                        <tr>
                            <th class="ps-4">No. Opname & Tanggal</th>
                            <th>No. Berita Acara (BA)</th>
                            <th>Site / Gudang</th>
                            <th>Pelaksana Opname</th>
                            <th>Total SKU & Selisih Fisik</th>
                            <th>Nilai Variansi (Rp)</th>
                            <th>Status Approval</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="fs-7">
                        @forelse($opnames as $op)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bolder text-gray-900 fs-6 d-block">{{ $op->opname_number }}</span>
                                    <span class="text-muted fs-8">{{ $op->opname_date->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-light-primary fw-bold fs-8">
                                        {{ $op->berita_acara_number ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-gray-800 fw-semibold">{{ $op->site->site_name ?? 'Gudang Pusat' }}</span>
                                </td>
                                <td>
                                    <span class="text-gray-900 fw-semibold">{{ $op->conductedBy->full_name ?? '-' }}</span>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-bold text-gray-900">{{ $op->items->count() }} SKU</span>
                                        @if($op->total_variance_qty > 0)
                                            <span class="badge badge-light-danger fw-bold fs-9 ms-1">
                                                Selisih {{ number_format($op->total_variance_qty, 0) }} unit
                                            </span>
                                        @else
                                            <span class="badge badge-light-success fw-bold fs-9 ms-1">Cocok (0 Selisih)</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold {{ $op->total_variance_value < 0 ? 'text-danger' : ($op->total_variance_value > 0 ? 'text-primary' : 'text-success') }}">
                                        Rp {{ number_format($op->total_variance_value, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $op->status_badge['class'] }} fs-8 fw-bold">
                                        {{ $op->status_badge['label'] }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <button
                                            type="button"
                                            wire:click="openDetail('{{ $op->id }}')"
                                            class="btn btn-sm btn-icon btn-light-primary"
                                            title="Lihat Rincian Item"
                                        >
                                            <i class="ki-outline ki-eye fs-4"></i>
                                        </button>

                                        <button
                                            type="button"
                                            wire:click="openBeritaAcara('{{ $op->id }}')"
                                            class="btn btn-sm btn-light-dark fw-bold fs-8"
                                            title="Cetak / Tinjau Berita Acara"
                                        >
                                            <i class="ki-outline ki-document fs-5 me-1"></i> Berita Acara
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10">
                                    <i class="ki-outline ki-clipboard fs-3x text-muted mb-2"></i>
                                    <div class="fs-6 fw-bold text-gray-600">Belum ada sesi Stock Opname</div>
                                    <div class="fs-8 text-muted">Klik tombol "+ Mulai Sesi Stock Opname" di atas untuk memulai pencocokan stok fisik.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($opnames->hasPages())
                <div class="p-4 border-top">
                    {{ $opnames->links() }}
                </div>
            @endif
        </div>
    </div>
    <!--end::Table Opname List-->

    <!--begin::Modal Form Stock Opname-->
    @if($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered mw-1000px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-4 px-6 bg-light">
                        <h4 class="modal-title fw-bolder text-gray-900">
                            <i class="ki-outline ki-clipboard text-primary fs-2 me-2"></i> Input Sesi Stock Opname Baru
                        </h4>
                        <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <form wire:submit="saveOpname">
                        <div class="modal-body py-5 px-6" style="max-height: 70vh; overflow-y: auto;">
                            <!-- Header Form Data -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label required fs-7 fw-bold">Tanggal Pelaksanaan Opname</label>
                                    <input type="date" wire:model="opname_date" class="form-control form-control-solid fs-7" />
                                    @error('opname_date') <span class="text-danger fs-8">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-7 fw-bold">Site / Gudang Lokasi</label>
                                    <select wire:model="site_id" class="form-select form-select-solid fs-7">
                                        <option value="">Gudang Pusat / Default</option>
                                        @foreach($sites as $site)
                                            <option value="{{ $site->id }}">{{ $site->site_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-7 fw-bold">Catatan Pelaksanaan</label>
                                    <input type="text" wire:model="notes" class="form-control form-control-solid fs-7" placeholder="Opname Rutin Akhir Bulan..." />
                                </div>
                                <div class="col-12">
                                    <label class="form-label fs-7 fw-bold">Penjelasan / Alasan Selisih (Discrepancy Reason)</label>
                                    <textarea wire:model="discrepancy_reason" class="form-control form-control-solid fs-7" rows="2" placeholder="Tuliskan keterangan jika ada temuan selisih fisik (rusak, hilang, belum tercatat, dsb)..."></textarea>
                                </div>
                            </div>

                            <div class="separator my-4"></div>

                            <!-- Tabel Item Part Pencocokan Fisik -->
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h5 class="fs-6 fw-bold text-gray-900 mb-0">
                                    <i class="ki-outline ki-cube-2 fs-4 text-primary me-1"></i> Data Pencocokan Stok Suku Cadang
                                </h5>
                                <span class="badge badge-light-info fs-8 fw-bold">{{ count($opname_items) }} Item Suku Cadang</span>
                            </div>

                            <div class="table-responsive border rounded-3">
                                <table class="table table-bordered table-sm gs-3 gy-2 fs-8 bg-white mb-0 align-middle">
                                    <thead class="bg-light fw-bold text-gray-700 text-uppercase">
                                        <tr>
                                            <th>Part Number & Nama</th>
                                            <th style="min-width: 130px;">Lokasi Rak / Bin</th>
                                            <th class="text-center" style="width: 100px;">Stok Sistem</th>
                                            <th class="text-center" style="width: 120px;">Hitungan Fisik</th>
                                            <th class="text-center" style="width: 100px;">Selisih (+/-)</th>
                                            <th class="text-end" style="width: 120px;">Harga Satuan</th>
                                            <th class="text-end" style="width: 130px;">Nilai Selisih</th>
                                            <th style="min-width: 140px;">Keterangan Item</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($opname_items as $idx => $item)
                                            @php
                                                $diff = (float)($item['physical_stock'] ?? 0) - (float)($item['system_stock'] ?? 0);
                                                $varVal = $diff * (float)($item['unit_cost'] ?? 0);
                                            @endphp
                                            <tr class="{{ $diff != 0 ? 'bg-light-warning' : '' }}">
                                                <td>
                                                    <span class="fw-bold text-gray-900 d-block">{{ $item['part_number'] }}</span>
                                                    <span class="text-muted fs-9">{{ $item['part_name'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-light-primary fs-9 py-1 px-2 text-start text-truncate d-inline-block" style="max-width: 180px;" title="{{ $item['rack_location'] ?? '-' }}">
                                                        <i class="ki-outline ki-geolocation fs-9 me-1 text-primary"></i>
                                                        {{ $item['rack_location'] ?? 'Rak A-01' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="fw-bold text-gray-800">{{ number_format($item['system_stock'], 0) }}</span>
                                                    <span class="text-muted fs-9">{{ $item['uom'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <input
                                                        type="number"
                                                        step="1"
                                                        wire:model.live.debounce.300ms="opname_items.{{ $idx }}.physical_stock"
                                                        class="form-control form-control-sm fs-8 text-center fw-bolder {{ $diff != 0 ? 'border-warning' : '' }}"
                                                    />
                                                </td>
                                                <td class="text-center">
                                                    @if($diff > 0)
                                                        <span class="badge badge-light-primary fw-bolder">+{{ number_format($diff, 0) }}</span>
                                                    @elseif($diff < 0)
                                                        <span class="badge badge-light-danger fw-bolder">{{ number_format($diff, 0) }}</span>
                                                    @else
                                                        <span class="badge badge-light-success fw-bold">0</span>
                                                    @endif
                                                </td>
                                                <td class="text-end text-gray-700">
                                                    Rp {{ number_format($item['unit_cost'], 0, ',', '.') }}
                                                </td>
                                                <td class="text-end fw-bold {{ $varVal < 0 ? 'text-danger' : ($varVal > 0 ? 'text-primary' : 'text-gray-700') }}">
                                                    Rp {{ number_format($varVal, 0, ',', '.') }}
                                                </td>
                                                <td>
                                                    <input
                                                        type="text"
                                                        wire:model="opname_items.{{ $idx }}.discrepancy_notes"
                                                        class="form-control form-control-sm fs-9"
                                                        placeholder="Catatan kondisi..."
                                                    />
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="modal-footer border-top py-3 px-6">
                            <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-light fs-7">Batal</button>
                            <button type="submit" class="btn btn-primary fs-7 fw-bold">
                                <i class="ki-outline ki-check fs-4 me-1"></i> Simpan & Ajukan Opname ke Approval
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Form Stock Opname-->

    <!--begin::Modal Detail Opname-->
    @if($showDetailModal && $selectedOpname)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered mw-950px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-4 px-6 bg-light">
                        <div>
                            <h4 class="modal-title fw-bolder text-gray-900">
                                <i class="ki-outline ki-clipboard text-primary fs-2 me-2"></i> Rincian Stock Opname {{ $selectedOpname->opname_number }}
                            </h4>
                            <span class="text-muted fs-8">Nomor Berita Acara: <strong>{{ $selectedOpname->berita_acara_number ?? '-' }}</strong></span>
                        </div>
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <div class="modal-body p-6" style="max-height: 70vh; overflow-y: auto;">
                        <div class="row g-3 mb-4">
                            <div class="col-sm-3">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="fs-8 text-muted">Tanggal Opname</div>
                                    <div class="fs-7 fw-bold text-gray-900">{{ $selectedOpname->opname_date->format('d F Y') }}</div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="fs-8 text-muted">Site / Gudang</div>
                                    <div class="fs-7 fw-bold text-gray-900">{{ $selectedOpname->site->site_name ?? 'Gudang Pusat' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="fs-8 text-muted">Petugas Opname</div>
                                    <div class="fs-7 fw-bold text-gray-900">{{ $selectedOpname->conductedBy->full_name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="fs-8 text-muted">Status Approval</div>
                                    <div><span class="badge {{ $selectedOpname->status_badge['class'] }} fs-8 fw-bold">{{ $selectedOpname->status_badge['label'] }}</span></div>
                                </div>
                            </div>
                        </div>

                        @if($selectedOpname->discrepancy_reason)
                            <div class="alert alert-warning d-flex align-items-center p-4 mb-4">
                                <i class="ki-outline ki-information-5 fs-2x text-warning me-3"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Berita Acara Alasan Selisih:</h6>
                                    <div class="fs-7 text-gray-800">{{ $selectedOpname->discrepancy_reason }}</div>
                                </div>
                            </div>
                        @endif

                        <div class="table-responsive border rounded-3 mb-4">
                            <table class="table table-bordered table-sm gs-3 gy-2 fs-8 bg-white mb-0 align-middle">
                                <thead class="bg-light fw-bold text-gray-700 text-uppercase">
                                    <tr>
                                        <th>Part Number & Nama</th>
                                        <th>Lokasi Rak / Bin</th>
                                        <th class="text-center">Stok Sistem</th>
                                        <th class="text-center">Stok Fisik</th>
                                        <th class="text-center">Selisih</th>
                                        <th class="text-end">Harga Satuan</th>
                                        <th class="text-end">Nilai Selisih</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedOpname->items as $item)
                                        <tr class="{{ $item->difference_qty != 0 ? 'bg-light-warning' : '' }}">
                                            <td>
                                                <span class="fw-bold text-gray-900 d-block">{{ $item->part_number }}</span>
                                                <span class="text-muted fs-9">{{ $item->part_name }}</span>
                                            </td>
                                            <td>
                                                <span class="badge badge-light-primary fs-9 py-0.5 px-2">
                                                    <i class="ki-outline ki-geolocation fs-9 me-1"></i> {{ $item->rack_location ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ number_format($item->system_stock, 0) }} {{ $item->uom }}</td>
                                            <td class="text-center fw-bolder">{{ number_format($item->physical_stock, 0) }} {{ $item->uom }}</td>
                                            <td class="text-center">
                                                @if($item->difference_qty > 0)
                                                    <span class="badge badge-light-primary fw-bolder">+{{ number_format($item->difference_qty, 0) }}</span>
                                                @elseif($item->difference_qty < 0)
                                                    <span class="badge badge-light-danger fw-bolder">{{ number_format($item->difference_qty, 0) }}</span>
                                                @else
                                                    <span class="badge badge-light-success fw-bold">0</span>
                                                @endif
                                            </td>
                                            <td class="text-end">Rp {{ number_format($item->unit_cost, 0, ',', '.') }}</td>
                                            <td class="text-end fw-bold {{ $item->variance_cost < 0 ? 'text-danger' : ($item->variance_cost > 0 ? 'text-primary' : 'text-gray-700') }}">
                                                Rp {{ number_format($item->variance_cost, 0, ',', '.') }}
                                            </td>
                                            <td>{{ $item->discrepancy_notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Cards -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-light-primary rounded-3 d-flex justify-content-between align-items-center">
                                    <span class="fs-8 fw-bold text-primary">Total Selisih Unit Fisik:</span>
                                    <span class="fs-6 fw-bolder text-primary">{{ number_format($selectedOpname->total_variance_qty, 0) }} Unit</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light-danger rounded-3 d-flex justify-content-between align-items-center">
                                    <span class="fs-8 fw-bold text-danger">Total Nilai Variansi / Selisih:</span>
                                    <span class="fs-6 fw-bolder text-danger">Rp {{ number_format($selectedOpname->total_variance_value, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top py-3 px-6 d-flex justify-content-between">
                        <button type="button" wire:click="openBeritaAcara('{{ $selectedOpname->id }}')" class="btn btn-light-dark fs-7 fw-bold">
                            <i class="ki-outline ki-document fs-4 me-1"></i> Tinjau Berita Acara Resmi
                        </button>

                        <div class="d-flex gap-2">
                            @if($selectedOpname->status === 'submitted')
                                <button type="button" wire:click="rejectOpname('{{ $selectedOpname->id }}')" class="btn btn-light-danger fs-7 fw-bold">
                                    Tolak Opname
                                </button>
                                <button type="button" wire:click="approveOpname('{{ $selectedOpname->id }}')" class="btn btn-success fs-7 fw-bold">
                                    <i class="ki-outline ki-check-circle fs-4 me-1"></i> Setujui & Update Stok Fisik
                                </button>
                            @else
                                <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-light fs-7">Tutup</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Detail Opname-->

    <!--begin::Modal Berita Acara Stock Opname (Official Report)-->
    @if($showBeritaAcaraModal && $selectedOpname)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.6);">
            <div class="modal-dialog modal-dialog-centered mw-900px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-3 px-6 bg-light d-flex justify-content-between align-items-center">
                        <span class="fw-bolder text-gray-900 fs-6">Dokumen Resmi: Berita Acara Stock Opname</span>
                        <div class="d-flex gap-2">
                            <button type="button" onclick="window.print()" class="btn btn-sm btn-light-primary fw-bold">
                                <i class="ki-outline ki-printer fs-5 me-1"></i> Cetak / PDF
                            </button>
                            <button type="button" wire:click="$set('showBeritaAcaraModal', false)" class="btn btn-sm btn-icon btn-light">
                                <i class="ki-outline ki-cross fs-4"></i>
                            </button>
                        </div>
                    </div>

                    <div class="modal-body p-8 bg-white" id="beritaAcaraPrintArea">
                        <!-- Kop Surat Berita Acara -->
                        <div class="text-center pb-6 border-bottom mb-6">
                            <h3 class="fw-bolder text-gray-900 text-uppercase tracking-wide mb-1">BERITA ACARA PENYESUAIAN HASIL STOCK OPNAME</h3>
                            <div class="fs-7 text-muted">Nomor Berita Acara: <strong class="text-gray-900">{{ $selectedOpname->berita_acara_number }}</strong></div>
                            <div class="fs-8 text-muted">Sesuai Sesi Stock Opname: <strong>{{ $selectedOpname->opname_number }}</strong></div>
                        </div>

                        <!-- Keterangan Umum -->
                        <div class="mb-5 fs-7 text-gray-800 lh-lg">
                            Pada hari ini tanggal <strong>{{ $selectedOpname->opname_date->translatedFormat('l, d F Y') }}</strong>, telah dilaksanakan kegiatan Stock Opname suku cadang dan material bertempat di <strong>{{ $selectedOpname->site->site_name ?? 'Gudang Pusat Logistik SCM' }}</strong> dengan hasil pencatatan sebagai berikut:
                        </div>

                        <!-- Tabel Ringkasan Selisih -->
                        <div class="table-responsive border rounded-3 mb-5">
                            <table class="table table-bordered table-sm gs-3 gy-2 fs-8 bg-white mb-0 align-middle">
                                <thead class="bg-light fw-bold text-gray-800 text-uppercase">
                                    <tr>
                                        <th style="width: 30px;" class="text-center">No</th>
                                        <th>Part Number</th>
                                        <th>Nama Suku Cadang</th>
                                        <th>Lokasi Rak / Bin</th>
                                        <th class="text-center">Stok Sistem</th>
                                        <th class="text-center">Stok Fisik</th>
                                        <th class="text-center">Selisih (+/-)</th>
                                        <th class="text-end">Nilai Variansi (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedOpname->items as $item)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="fw-bold">{{ $item->part_number }}</td>
                                            <td>{{ $item->part_name }}</td>
                                            <td>{{ $item->rack_location ?? '-' }}</td>
                                            <td class="text-center">{{ number_format($item->system_stock, 0) }} {{ $item->uom }}</td>
                                            <td class="text-center fw-bold">{{ number_format($item->physical_stock, 0) }} {{ $item->uom }}</td>
                                            <td class="text-center fw-bold {{ $item->difference_qty < 0 ? 'text-danger' : ($item->difference_qty > 0 ? 'text-primary' : 'text-success') }}">
                                                {{ $item->difference_qty > 0 ? '+'.number_format($item->difference_qty, 0) : number_format($item->difference_qty, 0) }}
                                            </td>
                                            <td class="text-end fw-bold">
                                                Rp {{ number_format($item->variance_cost, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light fw-bolder">
                                    <tr>
                                        <td colspan="6" class="text-end">Total Variansi Selisih:</td>
                                        <td class="text-center">{{ number_format($selectedOpname->total_variance_qty, 0) }} Unit</td>
                                        <td class="text-end text-danger">Rp {{ number_format($selectedOpname->total_variance_value, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Alasan Selisih & Kesimpulan -->
                        <div class="p-4 bg-light rounded-3 mb-6 fs-7">
                            <div class="fw-bold text-gray-900 mb-1">Keterangan & Penyebab Selisih (Discrepancy Notes):</div>
                            <div class="text-gray-700 italic">
                                {{ $selectedOpname->discrepancy_reason ?: 'Tidak ada catatan khusus selisih material. Hasil perhitungan fisik telah diverifikasi oleh tim logistik.' }}
                            </div>
                        </div>

                        <!-- Kolom Tanda Tangan Resmi -->
                        <div class="row text-center pt-6 g-4 fs-7">
                            <div class="col-4">
                                <div class="text-muted fs-8 mb-12">Petugas Penghitung (Fisik)</div>
                                <div class="fw-bold text-gray-900 text-decoration-underline">{{ $selectedOpname->conductedBy->full_name ?? '-' }}</div>
                                <div class="fs-8 text-muted">Warehouse / Inventory Staff</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted fs-8 mb-12">Diperiksa Oleh</div>
                                <div class="fw-bold text-gray-900 text-decoration-underline">Supervisor Logistik Site</div>
                                <div class="fs-8 text-muted">Logistics Head</div>
                            </div>
                            <div class="col-4">
                                <div class="text-muted fs-8 mb-12">Disetujui Oleh</div>
                                <div class="fw-bold text-gray-900 text-decoration-underline">{{ $selectedOpname->approver->full_name ?? 'SCM & Plant Manager' }}</div>
                                <div class="fs-8 text-muted">Supply Chain Manager</div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top py-3 px-6">
                        <button type="button" wire:click="$set('showBeritaAcaraModal', false)" class="btn btn-light fs-7">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Berita Acara Stock Opname-->
</div>
