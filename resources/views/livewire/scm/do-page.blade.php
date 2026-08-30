<div>
    <!--begin::Header-->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-6 gap-3">
        <div>
            <h1 class="fs-2hx fw-bolder text-gray-900 mb-1">
                <i class="ki-outline ki-delivery-3 fs-1 text-primary me-2"></i> Delivery Order (DO Pengiriman)
            </h1>
            <span class="fs-7 text-muted fw-semibold">Surat Jalan pengiriman barang dari Supplier / Head Office menuju Site Operasional Tambang & pelacakan status ekspedisi.</span>
        </div>
        <button type="button" wire:click="openCreateModal" class="btn btn-sm btn-primary fw-bold">
            <i class="ki-outline ki-plus fs-3 me-1"></i> Buat Delivery Order (DO)
        </button>
    </div>
    <!--end::Header-->

    <!--begin::Table Card-->
    <div class="card card-flush shadow-sm border border-gray-200 bg-white">
        <div class="card-header pt-6 pb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between w-100 gap-3">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="position-relative">
                        <i class="ki-outline ki-magnifier fs-3 position-absolute top-50 translate-middle-y ms-4 text-gray-500"></i>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-solid form-control-sm w-200px w-lg-250px ps-11 fs-7" placeholder="Cari No. DO, Ekspedisi, Resi..." />
                    </div>
                    <select wire:model.live="filterStatus" class="form-select form-select-solid form-select-sm w-175px fs-7">
                        <option value="all">Semua Status</option>
                        <option value="in_transit">In Transit</option>
                        <option value="arrived">Tiba di Site</option>
                        <option value="partially_received">Diterima Sebagian</option>
                        <option value="received">Diterima Lengkap & Di-GR</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed align-middle gy-4 gs-4 fs-7">
                    <thead class="bg-light text-muted fw-bold fs-7 text-uppercase">
                        <tr>
                            <th>No. DO & Tanggal</th>
                            <th>No. PO & Vendor</th>
                            <th>Tujuan Site</th>
                            <th>Ekspedisi & Driver</th>
                            <th>Estimasi Tiba</th>
                            <th>Status Pengiriman</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dos as $do)
                            <tr>
                                <td>
                                    <a href="javascript:void(0)" wire:click="openDetailModal('{{ $do->id }}')" class="text-gray-900 fw-bold text-hover-primary fs-6 d-block">
                                        {{ $do->do_number }}
                                    </a>
                                    <span class="text-muted fs-8">{{ $do->do_date->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-light-primary fw-bold fs-8">{{ $do->purchaseOrder->po_number ?? 'PO' }}</span>
                                    <span class="text-gray-800 fw-semibold d-block fs-8 mt-1">{{ $do->purchaseOrder->vendor->name ?? 'Vendor' }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-gray-900">{{ $do->destinationSite->site_name ?? $do->destination_location_name }}</span>
                                </td>
                                <td>
                                    <span class="text-gray-900 fw-semibold fs-8 d-block">{{ $do->expedition_name ?: 'Internal Courier' }}</span>
                                    <span class="text-muted fs-9">Nopol: {{ $do->vehicle_plate_number ?: '-' }} | Resi: {{ $do->tracking_number ?: '-' }}</span>
                                </td>
                                <td>
                                    <span class="text-gray-800 fs-8">{{ $do->estimated_arrival_date ? $do->estimated_arrival_date->format('d M Y H:i') : '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $do->status_badge['class'] }} fs-8 fw-bold">{{ $do->status_badge['label'] }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" wire:click="openDetailModal('{{ $do->id }}')" class="btn btn-icon btn-sm btn-light-primary" title="Detail DO">
                                            <i class="ki-outline ki-eye fs-4"></i>
                                        </button>
                                        <button type="button" onclick="window.open('{{ route('scm.do.print', $do->id) }}', '_blank')" class="btn btn-icon btn-sm btn-light-success" title="Cetak DO">
                                            <i class="ki-outline ki-printer fs-4"></i>
                                        </button>
                                        @if($do->status === 'in_transit')
                                            <button type="button" wire:click="markAsArrived('{{ $do->id }}')" class="btn btn-sm btn-light-info fw-bold fs-8" title="Tandai Tiba">
                                                Tiba di Site
                                            </button>
                                        @endif
                                        @if(in_array($do->status, ['in_transit', 'arrived', 'partially_received']))
                                            <a href="{{ route('scm.gr') }}?do_id={{ $do->id }}" class="btn btn-sm btn-light-success fw-bold fs-8" title="Terima & Masukkan ke Stok Gudang">
                                                <i class="ki-outline ki-package fs-5 me-1"></i> Terima di Gudang (GR)
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-muted fs-7">Belum ada Surat Jalan / Delivery Order yang diterbitkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $dos->links() }}</div>
        </div>
    </div>
    <!--end::Table Card-->

    <!--begin::Modal Form Buat DO-->
    @if($showCreateModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered mw-750px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-4 px-6 bg-light-warning">
                        <h4 class="modal-title fw-bolder text-gray-900">
                            <i class="ki-outline ki-delivery-3 fs-2 text-warning me-2"></i> Buat Delivery Order (DO Pengiriman)
                        </h4>
                        <button type="button" wire:click="$set('showCreateModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <form wire:submit="saveDeliveryOrder">
                        <div class="modal-body py-5 px-6" style="max-height: 75vh; overflow-y: auto;">
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label required fs-7 fw-bold">Pilih Purchase Order (PO) Aktif</label>
                                    <select wire:model.live="purchase_order_id" class="form-select form-select-solid fs-7" required>
                                        <option value="">-- Pilih Purchase Order --</option>
                                        @foreach($availablePos as $ap)
                                            <option value="{{ $ap->id }}">{{ $ap->po_number }} - {{ $ap->vendor->name ?? 'Vendor' }} ({{ $ap->status_badge['label'] }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fs-7 fw-bold">Lokasi Asal (Origin)</label>
                                    <input type="text" wire:model="origin_location" class="form-control form-control-solid fs-7" required />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fs-7 fw-bold">Tujuan Site / Tambang</label>
                                    <select wire:model="destination_site_id" class="form-select form-select-solid fs-7">
                                        <option value="">-- Pilih Site Tambang --</option>
                                        @foreach($sites as $st)
                                            <option value="{{ $st->id }}">{{ $st->site_name }} ({{ $st->site_code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Nama Ekspedisi / Driver</label>
                                    <input type="text" wire:model="expedition_name" class="form-control form-control-solid fs-7" placeholder="Contoh: Ekspedisi Dakota / Driver Budi" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">No. Polisi Kendaraan</label>
                                    <input type="text" wire:model="vehicle_plate_number" class="form-control form-control-solid fs-7" placeholder="Contoh: B 9123 ABC" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">No. Resi / AWB Tracking</label>
                                    <input type="text" wire:model="tracking_number" class="form-control form-control-solid fs-7" placeholder="Contoh: EXP-8899210" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Estimasi Tiba di Site</label>
                                    <input type="datetime-local" wire:model="estimated_arrival_date" class="form-control form-control-solid fs-7" />
                                </div>
                                <div class="col-12">
                                    <label class="form-label fs-7 fw-bold">Catatan Pengiriman</label>
                                    <input type="text" wire:model="do_notes" class="form-control form-control-solid fs-7" placeholder="Contoh: Kirim via jalur darat ekspedisi cargo" />
                                </div>
                            </div>

                            @if(!empty($do_items))
                                <!-- Items to Ship in DO -->
                                <h6 class="fs-7 fw-bold text-gray-900 mb-2">Item yang Akan Dikirim (Kuantiti Parsial)</h6>
                                <div class="table-responsive mb-2">
                                    <table class="table table-bordered table-sm gs-2 gy-2 fs-8 mb-0">
                                        <thead class="bg-light fw-bold text-gray-700">
                                            <tr>
                                                <th>Part Number & Nama</th>
                                                <th class="text-center" style="width: 80px;">Qty PO</th>
                                                <th class="text-center" style="width: 80px;">Sdh Kirim</th>
                                                <th class="text-center" style="width: 80px;">Sisa Order</th>
                                                <th class="text-center" style="width: 100px;">Qty Kirim Ini</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($do_items as $index => $item)
                                                <tr>
                                                    <td>
                                                        <span class="fw-bold text-gray-900 d-block">{{ $item['part_number'] }}</span>
                                                        <span class="text-muted fs-9">{{ $item['part_name'] }}</span>
                                                    </td>
                                                    <td class="text-center align-middle">{{ $item['qty_ordered'] }} {{ $item['uom'] }}</td>
                                                    <td class="text-center align-middle text-muted">{{ $item['qty_previously_shipped'] }} {{ $item['uom'] }}</td>
                                                    <td class="text-center align-middle fw-bold text-primary">{{ $item['qty_remaining'] }} {{ $item['uom'] }}</td>
                                                    <td class="align-middle">
                                                        <input type="number" step="any" min="0" max="{{ $item['qty_remaining'] }}" wire:model="do_items.{{ $index }}.qty_to_ship" class="form-control form-control-sm fs-8 text-center fw-bold text-warning" />
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @elseif($purchase_order_id)
                                <div class="alert alert-light-warning fs-8 p-3">Tidak ada item sisa yang dapat dikirim untuk PO ini.</div>
                            @endif
                        </div>

                        <div class="modal-footer border-top py-3 px-6">
                            <button type="button" wire:click="$set('showCreateModal', false)" class="btn btn-light fs-7">Batal</button>
                            <button type="submit" class="btn btn-warning fs-7 fw-bold" @if(empty($do_items)) disabled @endif>
                                <i class="ki-outline ki-delivery-3 fs-4 me-1"></i> Terbitkan Delivery Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Form Buat DO-->

    <!--begin::Modal Detail DO-->
    @if($showDetailModal && $selectedDo)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered mw-750px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-4 px-6 bg-light">
                        <div class="d-flex align-items-center">
                            <h4 class="modal-title fw-bolder text-gray-900 me-3">{{ $selectedDo->do_number }}</h4>
                            <span class="badge {{ $selectedDo->status_badge['class'] }} fs-8 fw-bold">{{ $selectedDo->status_badge['label'] }}</span>
                        </div>
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <div class="modal-body py-5 px-6">
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="fs-8 text-muted">Nomor PO Terkait</div>
                                <div class="fs-7 fw-bold text-gray-900">{{ $selectedDo->purchaseOrder->po_number ?? '-' }}</div>
                                <div class="fs-8 text-muted">Vendor: {{ $selectedDo->purchaseOrder->vendor->name ?? '-' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="fs-8 text-muted">Tujuan Site</div>
                                <div class="fs-7 fw-bold text-gray-900">{{ $selectedDo->destinationSite->site_name ?? $selectedDo->destination_location_name }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="fs-8 text-muted">Ekspedisi / Pengemudi</div>
                                <div class="fs-7 text-gray-800">{{ $selectedDo->expedition_name ?: 'Internal Logistics' }} (Nopol: {{ $selectedDo->vehicle_plate_number ?: '-' }})</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="fs-8 text-muted">Nomor Resi / AWB Tracking</div>
                                <div class="fs-7 text-gray-800">{{ $selectedDo->tracking_number ?: '-' }}</div>
                            </div>
                        </div>

                        <h6 class="fs-7 fw-bold text-gray-900 mb-2">Item Barang yang Dikirim & Status Penerimaan di Gudang</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm gs-3 gy-2 fs-8 mb-0">
                                <thead class="bg-light fw-bold text-gray-700">
                                    <tr>
                                        <th>Part Number</th>
                                        <th>Nama Suku Cadang</th>
                                        <th class="text-center">Qty Dikirim (DO)</th>
                                        <th class="text-center">Qty Diterima (GR)</th>
                                        <th class="text-center">Status Item</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedDo->items as $it)
                                        @php
                                            $receivedQty = $selectedDo->getItemReceivedQuantity($it->part_id, $it->part_number);
                                            $isComplete = $receivedQty >= (float) $it->qty_shipped;
                                        @endphp
                                        <tr>
                                            <td class="fw-bold">{{ $it->part_number }}</td>
                                            <td>{{ $it->part_name }}</td>
                                            <td class="text-center">{{ $it->qty_shipped }} {{ $it->uom }}</td>
                                            <td class="text-center fw-bold {{ $receivedQty > 0 ? 'text-success' : 'text-muted' }}">
                                                {{ $receivedQty }} {{ $it->uom }}
                                            </td>
                                            <td class="text-center">
                                                @if($isComplete && $receivedQty > 0)
                                                    <span class="badge badge-light-success fs-9 fw-bold">Diterima Penuh</span>
                                                @elseif($receivedQty > 0)
                                                    <span class="badge badge-light-warning fs-9 fw-bold">Diterima Sebagian</span>
                                                @else
                                                    <span class="badge badge-light-secondary fs-9 fw-bold">Belum Di-GR</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer border-top py-3 px-6 d-flex justify-content-between">
                        <div>
                            @if(in_array($selectedDo->status, ['in_transit', 'arrived', 'partially_received']))
                                <a href="{{ route('scm.gr') }}?do_id={{ $selectedDo->id }}" class="btn btn-success btn-sm fs-7">
                                    <i class="ki-outline ki-package fs-4 me-1"></i> Terima Barang di Gudang (Goods Receipt)
                                </a>
                            @endif
                        </div>
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-light fs-7">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Detail-->
</div>
