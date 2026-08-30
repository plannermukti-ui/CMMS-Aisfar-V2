<div>
    <!--begin::Header-->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-6 gap-3">
        <div>
            <h1 class="fs-2hx fw-bolder text-gray-900 mb-1">
                <i class="ki-outline ki-document fs-1 text-primary me-2"></i> Purchase Request (PR)
            </h1>
            <span class="fs-7 text-muted fw-semibold">Permintaan pengadaan pembelian suku cadang untuk persetujuan manajerial & pemrosesan penawaran vendor.</span>
        </div>
        <button type="button" wire:click="openCreateModal" class="btn btn-sm btn-primary fw-bold">
            <i class="ki-outline ki-plus fs-3 me-1"></i> Buat PR Manual
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
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-solid form-control-sm w-200px w-lg-250px ps-11 fs-7" placeholder="Cari No. PR, Item..." />
                    </div>
                    <select wire:model.live="filterStatus" class="form-select form-select-solid form-select-sm w-150px fs-7">
                        <option value="all">Semua Status</option>
                        <option value="submitted">Menunggu Approval</option>
                        <option value="approved">Approved</option>
                        <option value="rfq_created">RFQ Created</option>
                        <option value="po_created">PO Issued</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed align-middle gy-4 gs-4 fs-7">
                    <thead class="bg-light text-muted fw-bold fs-7 text-uppercase">
                        <tr>
                            <th>No. PR & Tanggal</th>
                            <th>Sumber Permintaan</th>
                            <th>Daftar Part / Kebutuhan</th>
                            <th>Prioritas</th>
                            <th>Pemohon</th>
                            <th>Status PR</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prs as $pr)
                            <tr>
                                <td>
                                    <a href="javascript:void(0)" wire:click="openDetailModal('{{ $pr->id }}')" class="text-gray-900 fw-bold text-hover-primary fs-6 d-block">
                                        {{ $pr->pr_number }}
                                    </a>
                                    <span class="text-muted fs-8">{{ $pr->pr_date->format('d M Y') }}</span>
                                </td>
                                <td>
                                    @if($pr->materialOrder)
                                        <span class="badge badge-light-primary fs-8 fw-bold">Dari MOL: {{ $pr->materialOrder->mol_number }}</span>
                                    @else
                                        <span class="badge badge-light-secondary fs-8">Manual Logistics</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1" style="max-width: 250px;">
                                        @foreach($pr->items as $it)
                                            <div class="d-flex align-items-center justify-content-between text-gray-800 fs-8">
                                                <span class="text-truncate" title="{{ $it->part_name }}">• {{ $it->part_name }}</span>
                                                <span class="badge badge-sm badge-light-info ms-2">{{ $it->quantity }} {{ $it->uom }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light-{{ $pr->priority === 'emergency' ? 'danger' : ($pr->priority === 'high' ? 'warning' : 'info') }} fs-8 fw-bold">
                                        {{ ucfirst($pr->priority) }}
                                    </span>
                                </td>
                                <td>{{ $pr->requester->full_name ?? 'User' }}</td>
                                <td>
                                    <span class="badge {{ $pr->status_badge['class'] }} fs-8 fw-bold">
                                        {{ $pr->status_badge['label'] }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" wire:click="openDetailModal('{{ $pr->id }}')" class="btn btn-icon btn-sm btn-light-primary" title="Detail PR">
                                            <i class="ki-outline ki-eye fs-4"></i>
                                        </button>
                                        @if($pr->status === 'submitted')
                                            <button type="button" wire:click="approvePr('{{ $pr->id }}')" class="btn btn-sm btn-light-success fw-bold fs-8" title="Setujui PR">
                                                Approve PR
                                            </button>
                                        @elseif($pr->status === 'approved')
                                            <a href="{{ route('scm.rfq') }}?pr_id={{ $pr->id }}" class="btn btn-sm btn-primary fw-bold fs-8" title="Buat RFQ Vendor">
                                                Bandingkan Vendor
                                            </a>
                                        @endif

                                        @if(in_array($pr->status, ['submitted', 'draft']))
                                            <button type="button" wire:click="deletePr('{{ $pr->id }}')" wire:confirm="Hapus permanen PR ini?" class="btn btn-icon btn-sm btn-light-danger" title="Hapus PR">
                                                <i class="ki-outline ki-trash fs-4"></i>
                                            </button>
                                        @endif
                                        @if(!in_array($pr->status, ['cancelled', 'draft']))
                                            <button type="button" wire:click="cancelPr('{{ $pr->id }}')" wire:confirm="Batalkan dokumen PR ini?" class="btn btn-icon btn-sm btn-light-danger" title="Batalkan PR">
                                                <i class="ki-outline ki-cross-circle fs-4"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-muted fs-7">Belum ada Purchase Request (PR).</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $prs->links() }}</div>
        </div>
    </div>
    <!--end::Table Card-->

    <!--begin::Modal Form Buat PR-->
    @if($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered mw-750px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-4 px-6 bg-light-primary">
                        <h4 class="modal-title fw-bolder text-gray-900">
                            <i class="ki-outline ki-document fs-2 text-primary me-2"></i> Buat Purchase Request (PR)
                        </h4>
                        <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <form wire:submit="savePr">
                        <div class="modal-body py-5 px-6" style="max-height: 75vh; overflow-y: auto;">
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label required fs-7 fw-bold">Prioritas Pembelian</label>
                                    <select wire:model="priority" class="form-select form-select-solid fs-7">
                                        <option value="low">Low (Rutin)</option>
                                        <option value="medium">Medium (Normal)</option>
                                        <option value="high">High (Mendesak)</option>
                                        <option value="emergency">Emergency (Unit Breakdown)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-7 fw-bold">Target Tanggal Dibutuhkan</label>
                                    <input type="date" wire:model="required_date" class="form-control form-control-solid fs-7" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-7 fw-bold">Catatan / Alasan</label>
                                    <input type="text" wire:model="remarks" class="form-control form-control-solid fs-7" placeholder="Kebutuhan sparepart overhaul" />
                                </div>
                            </div>

                            <div class="separator my-4"></div>

                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="fs-7 fw-bold text-gray-900 mb-0">Daftar Part yang Akan Dibeli</h6>
                                <button type="button" wire:click="addItem" class="btn btn-xs btn-light-primary fw-bold">
                                    <i class="ki-outline ki-plus fs-6 me-1"></i> Tambah Item
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm gs-2 gy-2 fs-8">
                                    <thead class="bg-light fw-bold text-gray-700">
                                        <tr>
                                            <th>Pilih Master Part (Opsional)</th>
                                            <th>Part Number</th>
                                            <th>Nama Part</th>
                                            <th style="width: 70px;">Qty</th>
                                            <th style="width: 70px;">Satuan</th>
                                            <th style="width: 120px;">Est. Harga (IDR)</th>
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
                                                    <input type="number" step="any" wire:model="items.{{ $index }}.quantity" class="form-control form-control-sm fs-8 text-center" min="0.01" required />
                                                </td>
                                                <td>
                                                    <input type="text" wire:model="items.{{ $index }}.uom" class="form-control form-control-sm fs-8 text-center" />
                                                </td>
                                                <td>
                                                    <input type="number" step="1000" wire:model="items.{{ $index }}.estimated_unit_price" class="form-control form-control-sm fs-8 text-end" />
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
                        </div>

                        <div class="modal-footer border-top py-3 px-6">
                            <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-light fs-7">Batal</button>
                            <button type="submit" class="btn btn-primary fs-7 fw-bold">
                                <i class="ki-outline ki-check fs-4 me-1"></i> Submit Purchase Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Form-->

    <!--begin::Modal Detail PR-->
    @if($showDetailModal && $selectedPr)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered mw-750px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-4 px-6 bg-light">
                        <div class="d-flex align-items-center">
                            <h4 class="modal-title fw-bolder text-gray-900 me-3">{{ $selectedPr->pr_number }}</h4>
                            <span class="badge {{ $selectedPr->status_badge['class'] }} fs-8 fw-bold">{{ $selectedPr->status_badge['label'] }}</span>
                        </div>
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <div class="modal-body py-5 px-6">
                        <div class="row g-3 mb-4">
                            <div class="col-sm-4">
                                <div class="fs-8 text-muted">Tanggal PR</div>
                                <div class="fs-7 fw-bold text-gray-900">{{ $selectedPr->pr_date->format('d F Y') }}</div>
                            </div>
                            <div class="col-sm-4">
                                <div class="fs-8 text-muted">Pemohon</div>
                                <div class="fs-7 fw-bold text-gray-900">{{ $selectedPr->requester->full_name ?? 'User' }}</div>
                            </div>
                            <div class="col-sm-4">
                                <div class="fs-8 text-muted">Prioritas</div>
                                <div class="fs-7 fw-bold text-gray-900">{{ ucfirst($selectedPr->priority) }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="fs-8 text-muted">Sumber Request</div>
                                <div class="fs-7 text-gray-900">{{ $selectedPr->materialOrder ? 'MOL: ' . $selectedPr->materialOrder->mol_number : 'Manual SCM Request' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="fs-8 text-muted">Catatan</div>
                                <div class="fs-7 text-gray-800">{{ $selectedPr->remarks ?: '-' }}</div>
                            </div>
                        </div>

                        <h6 class="fs-7 fw-bold text-gray-900 mb-2">Item Suku Cadang Permintaan Pembelian</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm gs-3 gy-2 fs-8 mb-0">
                                <thead class="bg-light fw-bold text-gray-700">
                                    <tr>
                                        <th>Part Number</th>
                                        <th>Nama Suku Cadang</th>
                                        <th>Jumlah</th>
                                        <th>Est. Harga Satuan</th>
                                        <th>Est. Total (IDR)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $grandEst = 0; @endphp
                                    @foreach($selectedPr->items as $it)
                                        @php $totalEst = $it->quantity * $it->estimated_unit_price; $grandEst += $totalEst; @endphp
                                        <tr>
                                            <td class="fw-bold">{{ $it->part_number }}</td>
                                            <td>{{ $it->part_name }}</td>
                                            <td>{{ $it->quantity }} {{ $it->uom }}</td>
                                            <td>Rp {{ number_format($it->estimated_unit_price, 0, ',', '.') }}</td>
                                            <td class="text-end fw-bold">Rp {{ number_format($totalEst, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-light fw-bolder">
                                        <td colspan="4" class="text-end">Estimasi Total Anggaran:</td>
                                        <td class="text-end text-primary">Rp {{ number_format($grandEst, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer border-top py-3 px-6 d-flex justify-content-between">
                        <div class="d-flex flex-wrap gap-2">
                            @if($selectedPr->status === 'submitted')
                                <button type="button" wire:click="approvePr('{{ $selectedPr->id }}')" class="btn btn-success btn-sm fs-7">
                                    <i class="ki-outline ki-check fs-4 me-1"></i> Approve PR Ini
                                </button>
                            @elseif($selectedPr->status === 'approved')
                                <a href="{{ route('scm.rfq') }}?pr_id={{ $selectedPr->id }}" class="btn btn-primary btn-sm fs-7">
                                    <i class="ki-outline ki-calculator fs-4 me-1"></i> Lanjut ke RFQ & Bandingkan Vendor
                                </a>
                            @endif
                            
                            @if(in_array($selectedPr->status, ['submitted', 'draft']))
                                <button type="button" wire:click="deletePr('{{ $selectedPr->id }}')" wire:confirm="Hapus permanen transaksi PR ini?" class="btn btn-danger btn-sm fs-7 fw-bold">
                                    <i class="ki-outline ki-trash fs-5 me-1"></i> Hapus
                                </button>
                            @endif
                            @if(!in_array($selectedPr->status, ['cancelled', 'draft']))
                                <button type="button" wire:click="cancelPr('{{ $selectedPr->id }}')" wire:confirm="Batalkan transaksi PR ini?" class="btn btn-danger btn-sm fs-7 fw-bold">
                                    <i class="ki-outline ki-cross-circle fs-5 me-1"></i> Batalkan
                                </button>
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
