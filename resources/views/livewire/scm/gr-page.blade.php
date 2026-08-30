<div>
    <!--begin::Header-->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-6 gap-3">
        <div>
            <h1 class="fs-2hx fw-bolder text-gray-900 mb-1">
                <i class="ki-outline ki-package fs-1 text-primary me-2"></i> Goods Receipt (GR Penerimaan Gudang)
            </h1>
            <span class="fs-7 text-muted fw-semibold">Penerimaan fisik barang di gudang site/HO, otomatis menambahkan stok suku cadang, dan mengupdate status sparepart pada Work Order unit.</span>
        </div>
        <button type="button" wire:click="openCreateModal" class="btn btn-sm btn-primary fw-bold">
            <i class="ki-outline ki-plus fs-3 me-1"></i> Input Penerimaan GR
        </button>
    </div>
    <!--end::Header-->

    <!--begin::Table Card-->
    <div class="card card-flush shadow-sm border border-gray-200 bg-white">
        <div class="card-header pt-6 pb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between w-100 gap-3">
                <div class="position-relative">
                    <i class="ki-outline ki-magnifier fs-3 position-absolute top-50 translate-middle-y ms-4 text-gray-500"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-solid form-control-sm w-200px w-lg-250px ps-11 fs-7" placeholder="Cari No. GR, Surat Jalan..." />
                </div>
            </div>
        </div>

        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed align-middle gy-4 gs-4 fs-7">
                    <thead class="bg-light text-muted fw-bold fs-7 text-uppercase">
                        <tr>
                            <th>No. GR & Tanggal</th>
                            <th>No. DO / Surat Jalan</th>
                            <th>No. PO & Vendor</th>
                            <th>Lokasi Site</th>
                            <th>Item Diterima & Masuk Stok</th>
                            <th>Petugas Penerima</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grs as $gr)
                            <tr>
                                <td>
                                    <a href="javascript:void(0)" wire:click="openDetailModal('{{ $gr->id }}')" class="text-gray-900 fw-bold text-hover-primary fs-6 d-block">
                                        {{ $gr->gr_number }}
                                    </a>
                                    <span class="text-muted fs-8">{{ $gr->gr_date->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-light-warning fw-bold fs-8">{{ $gr->delivery_order_number ?: ($gr->deliveryOrder->do_number ?? '-') }}</span>
                                </td>
                                <td>
                                    <span class="text-gray-900 fw-bold fs-8 d-block">{{ $gr->purchaseOrder->po_number ?? '-' }}</span>
                                    <span class="text-muted fs-9">{{ $gr->purchaseOrder->vendor->name ?? 'Vendor' }}</span>
                                </td>
                                <td>{{ $gr->site->site_name ?? 'Central Warehouse' }}</td>
                                <td>
                                    <div class="d-flex flex-column gap-1" style="max-width: 250px;">
                                        @foreach($gr->items as $it)
                                            <span class="text-gray-800 fs-8">• {{ $it->part_name }} (<strong class="text-success">+{{ $it->qty_received }}</strong>)</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td>{{ $gr->receiver->full_name ?? 'User' }}</td>
                                <td>
                                    <span class="badge {{ $gr->status_badge['class'] }} fs-8 fw-bold">{{ $gr->status_badge['label'] }}</span>
                                </td>
                                <td class="text-end">
                                    <button type="button" wire:click="openDetailModal('{{ $gr->id }}')" class="btn btn-icon btn-sm btn-light-primary" title="Detail GR">
                                        <i class="ki-outline ki-eye fs-4"></i>
                                    </button>
                                    <button type="button" onclick="window.open('{{ route('scm.gr.print', $gr->id) }}', '_blank')" class="btn btn-icon btn-sm btn-light-success" title="Cetak GR">
                                        <i class="ki-outline ki-printer fs-4"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-8 text-muted fs-7">Belum ada data penerimaan barang (Goods Receipt).</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $grs->links() }}</div>
        </div>
    </div>
    <!--end::Table Card-->

    <!--begin::Modal Form Buat GR-->
    @if($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered mw-750px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-4 px-6 bg-light-primary">
                        <h4 class="modal-title fw-bolder text-gray-900">
                            <i class="ki-outline ki-package fs-2 text-primary me-2"></i> Input Penerimaan Barang (Goods Receipt)
                        </h4>
                        <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <form wire:submit="saveGoodsReceipt">
                        <div class="modal-body py-5 px-6" style="max-height: 75vh; overflow-y: auto;">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Pilih Dari Surat Jalan / DO In Transit</label>
                                    <select wire:model.live="delivery_order_id" class="form-select form-select-solid fs-7">
                                        <option value="">-- Non-DO / Penerimaan Langsung --</option>
                                        @foreach($pendingDos as $pd)
                                            <option value="{{ $pd->id }}">{{ $pd->do_number }} (PO: {{ $pd->purchaseOrder->po_number ?? '-' }} - {{ $pd->status_badge['label'] }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Lokasi Site Penerimaan</label>
                                    <select wire:model="site_id" class="form-select form-select-solid fs-7">
                                        <option value="">-- Gudang Pusat / Central Warehouse --</option>
                                        @foreach($sites as $st)
                                            <option value="{{ $st->id }}">{{ $st->site_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">No. Surat Jalan Vendor / Ekspedisi</label>
                                    <input type="text" wire:model="delivery_order_number" class="form-control form-control-solid fs-7" placeholder="Contoh: SJ-2026-991" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Catatan Kondisi Barang</label>
                                    <input type="text" wire:model="notes" class="form-control form-control-solid fs-7" placeholder="Barang diterima lengkap dan segel utuh" />
                                </div>
                            </div>

                            <div class="separator my-4"></div>

                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="fs-7 fw-bold text-gray-900 mb-0">Item Suku Cadang yang Diterima</h6>
                                @if(!$delivery_order_id)
                                    <button type="button" wire:click="addItem" class="btn btn-xs btn-light-primary fw-bold">
                                        <i class="ki-outline ki-plus fs-6 me-1"></i> Tambah Item
                                    </button>
                                @endif
                            </div>

                            @if($delivery_order_id)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm gs-2 gy-2 fs-8">
                                        <thead class="bg-light fw-bold text-gray-700">
                                            <tr>
                                                <th>Part Number & Nama</th>
                                                <th class="text-center" style="width: 80px;">Qty DO</th>
                                                <th class="text-center" style="width: 80px;">Sdh Terima</th>
                                                <th class="text-center" style="width: 80px;">Sisa DO</th>
                                                <th class="text-center" style="width: 100px;">Qty Diterima</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($items as $index => $item)
                                                <tr>
                                                    <td>
                                                        <span class="fw-bold text-gray-900 d-block">{{ $item['part_number'] }}</span>
                                                        <span class="text-muted fs-9">{{ $item['part_name'] }}</span>
                                                    </td>
                                                    <td class="text-center align-middle">{{ $item['qty_shipped'] ?? '-' }}</td>
                                                    <td class="text-center align-middle text-muted">{{ $item['qty_previously_received'] ?? 0 }}</td>
                                                    <td class="text-center align-middle fw-bold text-primary">{{ $item['qty_remaining'] ?? '-' }}</td>
                                                    <td class="align-middle">
                                                        <input type="number" step="any" wire:model="items.{{ $index }}.qty_received" class="form-control form-control-sm fs-8 text-center fw-bold text-success" min="0" required />
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm gs-2 gy-2 fs-8">
                                        <thead class="bg-light fw-bold text-gray-700">
                                            <tr>
                                                <th>Pilih Master Part (Opsional)</th>
                                                <th>Part Number</th>
                                                <th>Nama Suku Cadang</th>
                                                <th style="width: 90px;">Qty Diterima</th>
                                                <th style="width: 35px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($items as $index => $item)
                                                <tr>
                                                    <td>
                                                        <select wire:model.live="items.{{ $index }}.part_id" class="form-select form-select-sm fs-8">
                                                            <option value="">-- Input Manual --</option>
                                                            @foreach($parts as $p)
                                                                <option value="{{ $p->id }}">{{ $p->part_number }} - {{ $p->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" wire:model="items.{{ $index }}.part_number" class="form-control form-control-sm fs-8" placeholder="Part Number" />
                                                    </td>
                                                    <td>
                                                        <input type="text" wire:model="items.{{ $index }}.part_name" class="form-control form-control-sm fs-8" placeholder="Nama Part" required />
                                                    </td>
                                                    <td>
                                                        <input type="number" step="any" wire:model="items.{{ $index }}.qty_received" class="form-control form-control-sm fs-8 text-center fw-bold text-success" min="0.01" required />
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        @if(count($items) > 1)
                                                            <button type="button" wire:click="removeItem({{ $index }})" class="btn btn-xs btn-icon btn-light-danger">
                                                                <i class="ki-outline ki-cross fs-6"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        <div class="modal-footer border-top py-3 px-6">
                            <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-light fs-7">Batal</button>
                            <button type="submit" class="btn btn-primary fs-7 fw-bold">
                                <i class="ki-outline ki-check fs-4 me-1"></i> Simpan GR & Tambah Stok
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Form-->

    <!--begin::Modal Detail GR-->
    @if($showDetailModal && $selectedGr)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered mw-700px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-4 px-6 bg-light">
                        <div class="d-flex align-items-center">
                            <h4 class="modal-title fw-bolder text-gray-900 me-3">{{ $selectedGr->gr_number }}</h4>
                            <span class="badge {{ $selectedGr->status_badge['class'] }} fs-8 fw-bold">{{ $selectedGr->status_badge['label'] }}</span>
                        </div>
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <div class="modal-body py-5 px-6">
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="fs-8 text-muted">Tanggal Terima</div>
                                <div class="fs-7 fw-bold text-gray-900">{{ $selectedGr->gr_date->format('d F Y') }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="fs-8 text-muted">Petugas Penerima</div>
                                <div class="fs-7 fw-bold text-gray-900">{{ $selectedGr->receiver->full_name ?? 'User' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="fs-8 text-muted">No. Surat Jalan Vendor/DO</div>
                                <div class="fs-7 fw-bold text-gray-900">{{ $selectedGr->delivery_order_number ?: '-' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="fs-8 text-muted">Lokasi Site / Gudang</div>
                                <div class="fs-7 fw-bold text-gray-900">{{ $selectedGr->site->site_name ?? 'Central Warehouse' }}</div>
                            </div>
                        </div>

                        <h6 class="fs-7 fw-bold text-gray-900 mb-2">Item yang Telah Ditambahkan ke Stok Gudang</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm gs-3 gy-2 fs-8 mb-0">
                                <thead class="bg-light fw-bold text-gray-700">
                                    <tr>
                                        <th>Part Number</th>
                                        <th>Nama Suku Cadang</th>
                                        <th class="text-center">Qty Masuk</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedGr->items as $it)
                                        <tr>
                                            <td class="fw-bold">{{ $it->part_number }}</td>
                                            <td>{{ $it->part_name }}</td>
                                            <td class="text-center text-success fw-bold">+{{ $it->qty_received }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer border-top py-3 px-6">
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-light fs-7">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Detail-->
</div>
