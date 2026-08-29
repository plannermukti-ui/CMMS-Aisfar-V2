<div>
    <!--begin::Header-->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-6 gap-3">
        <div>
            <h1 class="fs-2hx fw-bolder text-gray-900 mb-1">
                <i class="ki-outline ki-notepad-edit fs-1 text-primary me-2"></i> Purchase Order (PO)
            </h1>
            <span class="fs-7 text-muted fw-semibold">Penerbitan Surat Pesanan Pembelian resmi kepada Vendor terpilih, rincian PPN 11%, dan pembuatan Delivery Order (DO) pengiriman ke HO/Site.</span>
        </div>
    </div>
    <!--end::Header-->

    <!--begin::Table Card-->
    <div class="card card-flush shadow-sm border border-gray-200 bg-white">
        <div class="card-header pt-6 pb-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between w-100 gap-3">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="position-relative">
                        <i class="ki-outline ki-magnifier fs-3 position-absolute top-50 translate-middle-y ms-4 text-gray-500"></i>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-solid form-control-sm w-200px w-lg-250px ps-11 fs-7" placeholder="Cari No. PO, Vendor..." />
                    </div>
                    <select wire:model.live="filterStatus" class="form-select form-select-solid form-select-sm w-150px fs-7">
                        <option value="all">Semua Status</option>
                        <option value="approved">Approved</option>
                        <option value="sent_to_vendor">Sent to Vendor</option>
                        <option value="do_created">DO In Transit</option>
                        <option value="received">Completed</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed align-middle gy-4 gs-4 fs-7">
                    <thead class="bg-light text-muted fw-bold fs-7 text-uppercase">
                        <tr>
                            <th>No. PO & Tanggal</th>
                            <th>Vendor / Supplier</th>
                            <th>Nilai DPP</th>
                            <th>PPN (11%)</th>
                            <th>Grand Total</th>
                            <th>Status PO</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pos as $po)
                            <tr>
                                <td>
                                    <a href="javascript:void(0)" wire:click="openDetailModal('{{ $po->id }}')" class="text-gray-900 fw-bold text-hover-primary fs-6 d-block">
                                        {{ $po->po_number }}
                                    </a>
                                    <span class="text-muted fs-8">{{ $po->po_date->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-30px symbol-circle bg-light-primary text-primary me-2 fw-bold fs-8">
                                            {{ strtoupper(substr($po->vendor->name ?? 'V', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="text-gray-900 fw-bold fs-7">{{ $po->vendor->name ?? 'Vendor' }}</span>
                                            <span class="text-muted fs-9 d-block">TOP: {{ $po->payment_terms }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>Rp {{ number_format($po->subtotal_dpp - $po->discount_amount, 0, ',', '.') }}</td>
                                <td class="text-primary fw-semibold">+ Rp {{ number_format($po->ppn_amount, 0, ',', '.') }}</td>
                                <td>
                                    <span class="fs-6 fw-bolder text-gray-900">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $po->status_badge['class'] }} fs-8 fw-bold">{{ $po->status_badge['label'] }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" wire:click="openDetailModal('{{ $po->id }}')" class="btn btn-icon btn-sm btn-light-primary" title="Lihat PO">
                                            <i class="ki-outline ki-eye fs-4"></i>
                                        </button>
                                        @if(in_array($po->status, ['approved', 'sent_to_vendor']))
                                            <button type="button" wire:click="openGenerateDoModal('{{ $po->id }}')" class="btn btn-sm btn-light-warning fw-bold fs-8" title="Buat Delivery Order (Kirim to Site)">
                                                <i class="ki-outline ki-delivery-3 fs-5 me-1"></i> Generate DO
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-muted fs-7">Belum ada Purchase Order (PO) yang diterbitkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $pos->links() }}</div>
        </div>
    </div>
    <!--end::Table Card-->

    <!--begin::Modal Form Generate Delivery Order (DO)-->
    @if($showDoModal && $selectedPo)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-4 px-6 bg-light-warning">
                        <h4 class="modal-title fw-bolder text-gray-900">
                            <i class="ki-outline ki-delivery-3 fs-2 text-warning me-2"></i> Generate Delivery Order (DO Pengiriman to HO/Site)
                        </h4>
                        <button type="button" wire:click="$set('showDoModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <form wire:submit="generateDeliveryOrder">
                        <div class="modal-body py-5 px-6">
                            <div class="alert alert-light-primary p-3 rounded-3 mb-4 fs-8">
                                Menerbitkan Surat Jalan / Delivery Order untuk PO: <strong>{{ $selectedPo->po_number }}</strong> (Vendor: {{ $selectedPo->vendor->name }}).
                            </div>

                            <div class="row g-3 mb-4">
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
                        </div>

                        <div class="modal-footer border-top py-3 px-6">
                            <button type="button" wire:click="$set('showDoModal', false)" class="btn btn-light fs-7">Batal</button>
                            <button type="submit" class="btn btn-warning fs-7 fw-bold">
                                <i class="ki-outline ki-delivery-3 fs-4 me-1"></i> Terbitkan Delivery Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Generate DO-->

    <!--begin::Modal Detail PO Resmi-->
    @if($showDetailModal && $selectedPo)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered mw-800px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-4 px-6 bg-light">
                        <div class="d-flex align-items-center">
                            <h4 class="modal-title fw-bolder text-gray-900 me-3">{{ $selectedPo->po_number }}</h4>
                            <span class="badge {{ $selectedPo->status_badge['class'] }} fs-8 fw-bold">{{ $selectedPo->status_badge['label'] }}</span>
                        </div>
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <div class="modal-body py-5 px-6" style="max-height: 75vh; overflow-y: auto;">
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="fs-8 text-muted">Kepada Vendor:</div>
                                <div class="fs-6 fw-bold text-gray-900">{{ $selectedPo->vendor->name ?? 'Vendor' }}</div>
                                <div class="fs-8 text-gray-700">{{ $selectedPo->vendor->address ?: 'Alamat tidak tersedia' }}</div>
                                <div class="fs-8 text-muted mt-1">NPWP: {{ $selectedPo->vendor->npwp ?: '-' }} | Telp: {{ $selectedPo->vendor->phone ?: '-' }}</div>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <div class="fs-8 text-muted">Tanggal PO: <strong class="text-gray-900">{{ $selectedPo->po_date->format('d F Y') }}</strong></div>
                                <div class="fs-8 text-muted">Target Pengiriman: <strong class="text-gray-900">{{ $selectedPo->delivery_target_date ? $selectedPo->delivery_target_date->format('d F Y') : '-' }}</strong></div>
                                <div class="fs-8 text-muted">Termin Pembayaran: <strong class="text-gray-900">{{ $selectedPo->payment_terms }}</strong></div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <h6 class="fs-7 fw-bold text-gray-900 mb-2">Rincian Barang yang Dipesan</h6>
                        @if($selectedPo->purchaseRequest)
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-sm gs-3 gy-2 fs-8 mb-0">
                                    <thead class="bg-light fw-bold text-gray-700">
                                        <tr>
                                            <th>Part Number</th>
                                            <th>Nama Suku Cadang</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Est. Unit Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($selectedPo->purchaseRequest->items as $it)
                                            <tr>
                                                <td class="fw-bold">{{ $it->part_number }}</td>
                                                <td>{{ $it->part_name }}</td>
                                                <td class="text-center">{{ $it->quantity }} {{ $it->uom }}</td>
                                                <td class="text-end">Rp {{ number_format($it->estimated_unit_price, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <!-- Financial Summary Indonesia Tax -->
                        <div class="row justify-content-end mb-4">
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-sm gs-2 gy-1 fs-8 mb-0">
                                        <tr>
                                            <td class="text-muted">Dasar Pengenaan Pajak (DPP):</td>
                                            <td class="text-end fw-bold">Rp {{ number_format($selectedPo->subtotal_dpp, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Potongan Diskon:</td>
                                            <td class="text-end text-danger fw-bold">- Rp {{ number_format($selectedPo->discount_amount, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">PPN ({{ $selectedPo->ppn_percentage }}%):</td>
                                            <td class="text-end text-primary fw-bold">+ Rp {{ number_format($selectedPo->ppn_amount, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Ongkos Kirim:</td>
                                            <td class="text-end fw-bold">Rp {{ number_format($selectedPo->shipping_cost, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr class="border-top border-2 border-dark">
                                            <td class="fs-6 fw-bolder text-gray-900">Grand Total PO:</td>
                                            <td class="fs-6 fw-bolder text-end text-success">Rp {{ number_format($selectedPo->grand_total, 0, ',', '.') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top py-3 px-6 d-flex justify-content-between">
                        <div>
                            @if(in_array($selectedPo->status, ['approved', 'sent_to_vendor']))
                                <button type="button" wire:click="openGenerateDoModal('{{ $selectedPo->id }}')" class="btn btn-warning btn-sm fs-7">
                                    <i class="ki-outline ki-delivery-3 fs-4 me-1"></i> Generate Delivery Order to Site
                                </button>
                            @endif
                        </div>
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-light fs-7">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Detail PO-->
</div>
