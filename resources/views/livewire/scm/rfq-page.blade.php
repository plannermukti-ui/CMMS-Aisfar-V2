<div>
    <!--begin::Header-->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-6 gap-3">
        <div>
            <h1 class="fs-2hx fw-bolder text-gray-900 mb-1">
                <i class="ki-outline ki-calculator fs-1 text-primary me-2"></i> RFQ & Evaluasi Penawaran Vendor
            </h1>
            <span class="fs-7 text-muted fw-semibold">Bandingkan harga penawaran multi-vendor, diskon, kalkulasi PPN 11% Indonesia, ongkir, serta tentukan pemenang untuk diterbitkan Purchase Order (PO).</span>
        </div>
        <button type="button" wire:click="openCreateModal()" class="btn btn-sm btn-primary fw-bold">
            <i class="ki-outline ki-plus fs-3 me-1"></i> Input Penawaran Vendor
        </button>
    </div>
    <!--end::Header-->

    <!--begin::Table Card-->
    <div class="card card-flush shadow-sm border border-gray-200 bg-white">
        <div class="card-header pt-6 pb-4">
            <h3 class="card-title fw-bolder text-gray-900 fs-5">Daftar Paket Pengadaan Siap Evaluasi (PR Approved)</h3>
        </div>

        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed align-middle gy-4 gs-4 fs-7">
                    <thead class="bg-light text-muted fw-bold fs-7 text-uppercase">
                        <tr>
                            <th>No. PR</th>
                            <th>Kebutuhan Part</th>
                            <th>Penawaran Masuk</th>
                            <th>Penawaran Terpilih</th>
                            <th>Status Pengadaan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($approvedPrs as $pr)
                            <tr>
                                <td>
                                    <span class="text-gray-900 fw-bold fs-6 d-block">{{ $pr->pr_number }}</span>
                                    <span class="text-muted fs-8">{{ $pr->pr_date->format('d M Y') }}</span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1" style="max-width: 250px;">
                                        @foreach($pr->items as $it)
                                            <span class="text-gray-800 fs-8">• {{ $it->part_name }} ({{ $it->quantity }} {{ $it->uom }})</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light-primary fw-bold fs-8">
                                        {{ $pr->quotations->count() }} Vendor Masuk
                                    </span>
                                </td>
                                <td>
                                    @php $selectedQ = $pr->quotations->where('is_selected', true)->first(); @endphp
                                    @if($selectedQ)
                                        <span class="badge badge-light-success fw-bold fs-8">
                                            <i class="ki-outline ki-check-circle fs-6 text-success me-1"></i> {{ $selectedQ->vendor->name ?? 'Vendor' }}
                                        </span>
                                        <span class="text-success fw-bold d-block fs-8 mt-1">Rp {{ number_format($selectedQ->grand_total, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted fs-8">Belum ditentukan</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $pr->status_badge['class'] }} fs-8 fw-bold">{{ $pr->status_badge['label'] }}</span>
                                </td>
                                <td class="text-end">
                                    <button type="button" wire:click="openCompareModal('{{ $pr->id }}')" class="btn btn-sm btn-primary fw-bold fs-8">
                                        <i class="ki-outline ki-calculator fs-4 me-1"></i> Evaluasi & Bandingkan
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-muted fs-7">Belum ada PR yang siap untuk evaluasi RFQ.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $approvedPrs->links() }}</div>
        </div>
    </div>
    <!--end::Table Card-->

    <!--begin::Modal Perbandingan Harga Vendor (Compare Matrix)-->
    @if($showCompareModal && $comparePr)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered mw-950px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-4 px-6 bg-light-primary">
                        <div class="d-flex align-items-center">
                            <h4 class="modal-title fw-bolder text-gray-900 me-3">
                                Matriks Evaluasi Penawaran Vendor (PR: {{ $comparePr->pr_number }})
                            </h4>
                        </div>
                        <button type="button" wire:click="$set('showCompareModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <div class="modal-body py-5 px-6" style="max-height: 75vh; overflow-y: auto;">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <span class="fs-8 text-muted">Daftar Item Dibeli:</span>
                                <div class="fs-7 fw-bold text-gray-800">
                                    @foreach($comparePr->items as $it)
                                        {{ $it->part_name }} ({{ $it->quantity }} {{ $it->uom }}){{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </div>
                            </div>
                            <button type="button" wire:click="openCreateModal('{{ $comparePr->id }}')" class="btn btn-sm btn-light-primary fw-bold">
                                <i class="ki-outline ki-plus fs-5 me-1"></i> Input Penawaran Vendor Lain
                            </button>
                        </div>

                        <!-- Matrix Comparison Table -->
                        @if($comparePr->quotations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle gs-4 gy-3 fs-7 mb-0">
                                    <thead class="bg-light fw-bold text-gray-800 text-uppercase fs-8">
                                        <tr>
                                            <th>Vendor / Supplier</th>
                                            <th>No. Penawaran</th>
                                            <th>DPP (Subtotal)</th>
                                            <th>Diskon</th>
                                            <th>PPN (11%)</th>
                                            <th>Ongkir</th>
                                            <th>Grand Total (IDR)</th>
                                            <th>Lead Time</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($comparePr->quotations as $q)
                                            <tr class="{{ $q->is_selected ? 'bg-light-success' : '' }}">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="symbol symbol-30px symbol-circle bg-light-primary text-primary me-2 fw-bold fs-8">
                                                            {{ strtoupper(substr($q->vendor->name ?? 'V', 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <span class="text-gray-900 fw-bold">{{ $q->vendor->name ?? 'Vendor' }}</span>
                                                            <span class="text-muted fs-9 d-block">TOP: {{ $q->vendor->term_of_payment ?? 'Net 30' }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $q->quotation_number ?: '-' }}</td>
                                                <td>Rp {{ number_format($q->subtotal_dpp, 0, ',', '.') }}</td>
                                                <td class="text-danger">- Rp {{ number_format($q->discount_amount, 0, ',', '.') }}</td>
                                                <td class="text-primary">+ Rp {{ number_format($q->ppn_amount, 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($q->shipping_cost, 0, ',', '.') }}</td>
                                                <td class="fs-6 fw-bolder text-gray-900">
                                                    Rp {{ number_format($q->grand_total, 0, ',', '.') }}
                                                </td>
                                                <td>{{ $q->delivery_lead_time_days }} Hari</td>
                                                <td class="text-center">
                                                    @if($q->is_selected)
                                                        <span class="badge badge-success fs-8 fw-bold">Pemenang PO</span>
                                                    @else
                                                        <button
                                                            type="button"
                                                            wire:click="selectWinnerAndGeneratePo('{{ $q->id }}')"
                                                            wire:confirm="Pilih vendor ini sebagai pemenang dan terbitkan Purchase Order (PO) resmi?"
                                                            class="btn btn-sm btn-success fw-bold fs-8 px-3"
                                                        >
                                                            Pilih & Terbitkan PO
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 border rounded-3 bg-light">
                                <i class="ki-outline ki-calculator fs-3x text-muted mb-2"></i>
                                <div class="fs-6 fw-semibold text-gray-700">Belum ada penawaran harga vendor yang diinput.</div>
                                <span class="fs-8 text-muted">Klik tombol "Input Penawaran Vendor" untuk memasukkan harga dari vendor.</span>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer border-top py-3 px-6">
                        <button type="button" wire:click="$set('showCompareModal', false)" class="btn btn-light fs-7">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Perbandingan-->

    <!--begin::Modal Form Input Penawaran Vendor-->
    @if($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-4 px-6 bg-light-primary">
                        <h4 class="modal-title fw-bolder text-gray-900">
                            <i class="ki-outline ki-calculator fs-2 text-primary me-2"></i> Input Penawaran Vendor
                        </h4>
                        <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <form wire:submit="saveQuotation">
                        <div class="modal-body py-5 px-6">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label required fs-7 fw-bold">Pilih Purchase Request (PR)</label>
                                    <select wire:model="purchase_request_id" class="form-select form-select-solid fs-7" required>
                                        <option value="">-- Pilih PR --</option>
                                        @foreach($allPrs as $p)
                                            <option value="{{ $p->id }}">{{ $p->pr_number }} ({{ $p->items->pluck('part_name')->implode(', ') }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fs-7 fw-bold">Pilih Vendor / Supplier</label>
                                    <select wire:model="vendor_id" class="form-select form-select-solid fs-7" required>
                                        <option value="">-- Pilih Vendor --</option>
                                        @foreach($vendors as $v)
                                            <option value="{{ $v->id }}">{{ $v->name }} (TOP: {{ $v->term_of_payment }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">No. Surat Penawaran Vendor</label>
                                    <input type="text" wire:model="quotation_number" class="form-control form-control-solid fs-7" placeholder="Contoh: QTN-UT-2026-081" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Lead Time Pengiriman (Hari)</label>
                                    <input type="number" wire:model="delivery_lead_time_days" class="form-control form-control-solid fs-7" min="1" />
                                </div>
                            </div>

                            <div class="separator my-4"></div>

                            <!-- Perhitungan Finansial & Pajak Indonesia -->
                            <h6 class="fs-7 fw-bold text-gray-900 mb-3">Rincian Finansial, Diskon & PPN Indonesia</h6>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label required fs-7 fw-bold">Subtotal DPP (IDR)</label>
                                    <input type="number" step="1000" wire:model.live.debounce.300ms="subtotal_dpp" class="form-control form-control-solid fs-7" placeholder="Dasar Pengenaan Pajak" required />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Diskon Penawaran (IDR)</label>
                                    <input type="number" step="1000" wire:model.live.debounce.300ms="discount_amount" class="form-control form-control-solid fs-7" placeholder="Potongan harga" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-7 fw-bold">Tarif PPN (%)</label>
                                    <input type="number" step="0.1" wire:model.live.debounce.300ms="ppn_percentage" class="form-control form-control-solid fs-7" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-7 fw-bold">Nominal PPN (IDR)</label>
                                    <input type="text" class="form-control form-control-solid fs-7 bg-light fw-bold text-primary" value="Rp {{ number_format($ppn_amount, 0, ',', '.') }}" readonly />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-7 fw-bold">Ongkos Kirim (Freight)</label>
                                    <input type="number" step="1000" wire:model.live.debounce.300ms="shipping_cost" class="form-control form-control-solid fs-7" />
                                </div>
                            </div>

                            <!-- Grand Total Preview Card -->
                            <div class="card bg-light-success p-3 rounded-3 border border-success border-opacity-25 d-flex flex-row align-items-center justify-content-between">
                                <span class="fs-7 fw-bold text-gray-800">Grand Total Penawaran Vendor:</span>
                                <span class="fs-4 fw-bolder text-success">Rp {{ number_format($grand_total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="modal-footer border-top py-3 px-6">
                            <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-light fs-7">Batal</button>
                            <button type="submit" class="btn btn-primary fs-7 fw-bold">
                                <i class="ki-outline ki-check fs-4 me-1"></i> Simpan Penawaran Vendor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Form-->
</div>
