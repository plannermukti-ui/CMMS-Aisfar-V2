<div>
    <!--begin::Header-->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-6 gap-3">
        <div>
            <h1 class="fs-2hx fw-bolder text-gray-900 mb-1">
                <i class="ki-outline ki-handcart fs-1 text-primary me-2"></i> Mechanic Order Part (MOL)
            </h1>
            <span class="fs-7 text-muted fw-semibold">Permintaan pengeluaran barang/sparepart dari mekanik lapangan untuk unit Work Order.</span>
        </div>
        <button type="button" wire:click="openCreateModal" class="btn btn-sm btn-primary fw-bold">
            <i class="ki-outline ki-plus fs-3 me-1"></i> Buat Permintaan MOL
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
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-solid form-control-sm w-200px w-lg-250px ps-11 fs-7" placeholder="Cari No. MOL, Part..." />
                    </div>
                    <select wire:model.live="filterStatus" class="form-select form-select-solid form-select-sm w-150px fs-7">
                        <option value="all">Semua Status</option>
                        <option value="submitted">Submitted</option>
                        <option value="approved">Approved</option>
                        <option value="issued">Issued</option>
                        <option value="converted_to_pr">Converted to PR</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed align-middle gy-4 gs-4 fs-7">
                    <thead class="bg-light text-muted fw-bold fs-7 text-uppercase">
                        <tr>
                            <th>No. MOL & Tanggal</th>
                            <th>Work Order / Unit</th>
                            <th>Daftar Part Diminta</th>
                            <th>Pemohon</th>
                            <th>Status Ketersediaan</th>
                            <th>Status MOL</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mols as $mol)
                            @php
                                $totalReq = $mol->items->sum('qty_requested');
                                $totalIss = $mol->items->sum('qty_issued');
                                $totalShortage = 0;
                                $canIssueAny = false;

                                foreach($mol->items as $it) {
                                    $needed = max(0, (float)$it->qty_requested - (float)$it->qty_issued);
                                    $stock = (float)($it->part?->stock_on_hand ?? 0);
                                    if ($stock > 0 && $needed > 0) {
                                        $canIssueAny = true;
                                    }
                                    if ($needed > $stock) {
                                        $totalShortage += ($needed - $stock);
                                    }
                                }
                                $isFullyIssued = ($totalIss >= $totalReq && $totalReq > 0);
                            @endphp
                            <tr>
                                <td>
                                    <a href="javascript:void(0)" wire:click="openDetailModal('{{ $mol->id }}')" class="text-gray-900 fw-bold text-hover-primary fs-6 d-block">
                                        {{ $mol->mol_number }}
                                    </a>
                                    <span class="text-muted fs-8">{{ $mol->mol_date->format('d M Y') }}</span>
                                </td>
                                <td>
                                    @if($mol->workOrder)
                                        <span class="badge badge-light-primary fs-8 fw-bold">{{ $mol->workOrder->wo_number }}</span>
                                        <span class="text-gray-800 fw-semibold d-block fs-8 mt-1">{{ $mol->workOrder->equipment->unit ?? 'Unit' }}</span>
                                    @else
                                        <span class="text-muted fs-8">Permintaan Langsung</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1" style="max-width: 250px;">
                                        @foreach($mol->items as $item)
                                            <div class="d-flex align-items-center justify-content-between text-gray-800 fs-8">
                                                <span class="text-truncate" title="{{ $item->part_name }}">• {{ $item->part_name }}</span>
                                                <span class="badge badge-sm {{ $item->qty_issued >= $item->qty_requested ? 'badge-light-success' : ($item->qty_issued > 0 ? 'badge-light-primary' : 'badge-light-secondary') }} ms-2" title="Keluar {{ $item->qty_issued }} dari {{ $item->qty_requested }}">
                                                    {{ $item->qty_issued }}/{{ $item->qty_requested }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <span class="text-gray-900 fw-bold fs-8">{{ $mol->requester->full_name ?? 'User' }}</span>
                                </td>
                                <td>
                                    @if($isFullyIssued)
                                        <span class="badge badge-light-success fs-8 fw-bold">
                                            <i class="ki-outline ki-check-circle fs-6 text-success me-1"></i> Selesai Dikeluarkan
                                        </span>
                                    @elseif($totalShortage == 0 && $canIssueAny)
                                        <span class="badge badge-light-success fs-8 fw-bold">
                                            <i class="ki-outline ki-check-circle fs-6 text-success me-1"></i> Ready di Gudang
                                        </span>
                                    @elseif($canIssueAny && $totalShortage > 0)
                                        <span class="badge badge-light-warning fs-8 fw-bold" title="Tersedia sebagian, kurang {{ $totalShortage }}">
                                            <i class="ki-outline ki-information fs-6 text-warning me-1"></i> Stok Parsial (Kurang {{ $totalShortage }})
                                        </span>
                                    @else
                                        <span class="badge badge-light-danger fs-8 fw-bold" title="Stok habis, perlu PR">
                                            <i class="ki-outline ki-cross-circle fs-6 text-danger me-1"></i> Stok Kurang ({{ $totalShortage }})
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $mol->status_badge['class'] }} fs-8 fw-bold">
                                        {{ $mol->status_badge['label'] }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1 align-items-center">
                                        <button type="button" wire:click="openDetailModal('{{ $mol->id }}')" class="btn btn-icon btn-sm btn-light-primary" title="Lihat Detail MOL">
                                            <i class="ki-outline ki-eye fs-4"></i>
                                        </button>
                                        <button type="button" onclick="window.open('{{ route('scm.mol.print', $mol->id) }}', '_blank')" class="btn btn-icon btn-sm btn-light-success" title="Cetak MOL">
                                            <i class="ki-outline ki-printer fs-4"></i>
                                        </button>

                                        @if(in_array($mol->status, ['submitted', 'draft', 'partially_issued', 'approved']))
                                            @if($canIssueAny && $totalShortage > 0)
                                                <!-- Stok ada sebagian & kurang sebagian: Approve yang ada + buat PR untuk sisa -->
                                                <button type="button" wire:click="approveAndGeneratePr('{{ $mol->id }}')" class="btn btn-sm btn-success fw-bold fs-8 px-2" title="Keluarkan stok yang ada & langsung buat PR untuk sisa kekurangannya">
                                                    <i class="ki-outline ki-check-circle fs-7 me-1"></i> Approve & Buat PR Sisa
                                                </button>
                                            @elseif($canIssueAny && $totalShortage == 0)
                                                <!-- Stok cukup penuh: Approve & Issue -->
                                                <button type="button" wire:click="approveAndIssue('{{ $mol->id }}')" class="btn btn-sm btn-light-success fw-bold fs-8 px-2" title="Setujui & Keluarkan Barang dari Gudang">
                                                    <i class="ki-outline ki-check fs-7 me-1"></i> Approve & Issue
                                                </button>
                                            @elseif($totalShortage > 0)
                                                <!-- Stok kosong: Buat PR -->
                                                <button type="button" wire:click="generateToPr('{{ $mol->id }}')" class="btn btn-sm btn-light-warning fw-bold fs-8 px-2" title="Buat Purchase Request untuk pengadaan barang kosong">
                                                    <i class="ki-outline ki-document fs-7 me-1"></i> Buat PR ({{ $totalShortage }})
                                                </button>
                                            @endif
                                        @endif

                                        @if(in_array($mol->status, ['submitted', 'draft']))
                                            <button type="button" wire:click="deleteMol('{{ $mol->id }}')" wire:confirm="Hapus permanen transaksi MOL ini?" class="btn btn-icon btn-sm btn-light-danger" title="Hapus MOL">
                                                <i class="ki-outline ki-trash fs-4"></i>
                                            </button>
                                        @endif
                                        @if(!in_array($mol->status, ['cancelled', 'draft']))
                                            <button type="button" wire:click="cancelMol('{{ $mol->id }}')" wire:confirm="Batalkan transaksi MOL ini? Stok (jika sudah dikeluarkan) akan dikembalikan ke gudang." class="btn btn-icon btn-sm btn-light-danger" title="Batalkan MOL">
                                                <i class="ki-outline ki-cross-circle fs-4"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-muted fs-7">Belum ada data permintaan part (MOL).</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $mols->links() }}</div>
        </div>
    </div>
    <!--end::Table Card-->

    <!--begin::Modal Form Buat MOL-->
    @if($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered mw-750px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-4 px-6 bg-light-primary">
                        <h4 class="modal-title fw-bolder text-gray-900">
                            <i class="ki-outline ki-handcart fs-2 text-primary me-2"></i> Buat Mechanic Order Part (MOL)
                        </h4>
                        <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <form wire:submit="saveMol">
                        <div class="modal-body py-5 px-6" style="max-height: 75vh; overflow-y: auto;">
                            <!-- Header Form -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Hubungkan ke Work Order (Opsional)</label>
                                    <select wire:model="work_order_id" class="form-select form-select-solid fs-7">
                                        <option value="">-- Non-WO / Permintaan Langsung --</option>
                                        @foreach($workOrders as $wo)
                                            <option value="{{ $wo->id }}">{{ $wo->wo_number }} - {{ $wo->equipment->unit ?? 'Unit' }} ({{ $wo->job_title }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Catatan Permintaan</label>
                                    <input type="text" wire:model="notes" class="form-control form-control-solid fs-7" placeholder="Contoh: Kebutuhan darurat breakdown shift malam" />
                                </div>
                            </div>

                            <div class="separator my-4"></div>

                            <!-- Items Table Repeater -->
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="fs-7 fw-bold text-gray-900 mb-0">Daftar Suku Cadang yang Diminta</h6>
                                <button type="button" wire:click="addItem" class="btn btn-xs btn-light-primary fw-bold">
                                    <i class="ki-outline ki-plus fs-6 me-1"></i> Tambah Item
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm gs-2 gy-2 fs-8">
                                    <thead class="bg-light fw-bold text-gray-700">
                                        <tr>
                                            <th>Pilih Dari Master Part (Opsional)</th>
                                            <th>Part Number</th>
                                            <th>Nama Suku Cadang</th>
                                            <th style="width: 80px;">Qty</th>
                                            <th style="width: 90px;">Stok Saat Ini</th>
                                            <th style="width: 40px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $index => $item)
                                            <tr>
                                                <td>
                                                    <select wire:model.live="items.{{ $index }}.part_id" class="form-select form-select-sm fs-8">
                                                        <option value="">-- Input Manual --</option>
                                                        @foreach($parts as $p)
                                                            <option value="{{ $p->id }}">{{ $p->part_number }} - {{ $p->name }} (Stok: {{ $p->stock_on_hand }})</option>
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
                                                    <input type="number" step="any" wire:model="items.{{ $index }}.qty_requested" class="form-control form-control-sm fs-8 text-center" min="0.01" required />
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge {{ ($item['stock_available'] >= $item['qty_requested'] && $item['stock_available'] > 0) ? 'badge-light-success' : 'badge-light-danger' }} fs-9">
                                                        {{ $item['stock_available'] }}
                                                    </span>
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
                                <i class="ki-outline ki-check fs-4 me-1"></i> Submit Permintaan MOL
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Form-->

    <!--begin::Modal Detail MOL-->
    @if($showDetailModal && $selectedMol)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered mw-700px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-4 px-6 bg-light">
                        <div class="d-flex align-items-center">
                            <h4 class="modal-title fw-bolder text-gray-900 me-3">{{ $selectedMol->mol_number }}</h4>
                            <span class="badge {{ $selectedMol->status_badge['class'] }} fs-8 fw-bold">{{ $selectedMol->status_badge['label'] }}</span>
                        </div>
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <div class="modal-body py-5 px-6">
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="fs-8 text-muted">Tanggal Request</div>
                                <div class="fs-7 fw-bold text-gray-900">{{ $selectedMol->mol_date->format('d F Y') }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="fs-8 text-muted">Pemohon (Mekanik)</div>
                                <div class="fs-7 fw-bold text-gray-900">{{ $selectedMol->requester->full_name ?? 'User' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="fs-8 text-muted">Work Order Terkait</div>
                                <div class="fs-7 fw-bold text-gray-900">{{ $selectedMol->workOrder ? $selectedMol->workOrder->wo_number . ' (' . ($selectedMol->workOrder->equipment->unit ?? 'Unit') . ')' : 'Non-WO Direct' }}</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="fs-8 text-muted">Catatan</div>
                                <div class="fs-7 text-gray-800">{{ $selectedMol->notes ?: '-' }}</div>
                            </div>
                        </div>

                        @php
                            $detailTotalReq = $selectedMol->items->sum('qty_requested');
                            $detailTotalIss = $selectedMol->items->sum('qty_issued');
                            $detailShortage = 0;
                            $detailCanIssue = 0;

                            foreach($selectedMol->items as $it) {
                                $needed = max(0, (float)$it->qty_requested - (float)$it->qty_issued);
                                $stock = (float)($it->part?->stock_on_hand ?? 0);
                                $canIssueItem = min($needed, max(0, $stock));
                                $detailCanIssue += $canIssueItem;
                                $shortageItem = max(0, $needed - $canIssueItem);
                                $detailShortage += $shortageItem;
                            }
                        @endphp

                        @if(session()->has('message'))
                            <div class="alert alert-success d-flex align-items-center p-3 mb-4 rounded-3">
                                <i class="ki-outline ki-check-circle fs-3 text-success me-2"></i>
                                <div class="fs-7 fw-bold text-success">{{ session('message') }}</div>
                            </div>
                        @endif

                        <h6 class="fs-7 fw-bold text-gray-900 mb-2">Rincian Suku Cadang & Ketersediaan Gudang</h6>
                        <div class="table-responsive border rounded-3 mb-4">
                            <table class="table table-bordered table-sm gs-3 gy-2 fs-8 mb-0 align-middle">
                                <thead class="bg-light fw-bold text-gray-700 text-uppercase">
                                    <tr>
                                        <th>Part Number</th>
                                        <th>Nama Part</th>
                                        <th class="text-center" style="width: 80px;">Diminta</th>
                                        <th class="text-center" style="width: 90px;">Stok Gudang</th>
                                        <th class="text-center" style="width: 80px;">Dikeluarkan</th>
                                        <th class="text-center" style="width: 80px;">Sisa Kurang</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedMol->items as $it)
                                        @php
                                            $itemStock = (float)($it->part?->stock_on_hand ?? 0);
                                            $itemShortage = max(0, (float)$it->qty_requested - (float)$it->qty_issued);
                                        @endphp
                                        <tr>
                                            <td class="fw-bold">{{ $it->part_number }}</td>
                                            <td>{{ $it->part_name }}</td>
                                            <td class="text-center fw-bold text-gray-900">{{ number_format($it->qty_requested, 0) }} {{ $it->part->uom ?? 'Pcs' }}</td>
                                            <td class="text-center">
                                                <span class="badge {{ $itemStock > 0 ? 'badge-light-success text-success' : 'badge-light-danger text-danger' }} fw-bold fs-9">
                                                    {{ number_format($itemStock, 0) }}
                                                </span>
                                            </td>
                                            <td class="text-center fw-bold text-primary">{{ number_format($it->qty_issued, 0) }}</td>
                                            <td class="text-center">
                                                @if($itemShortage > 0)
                                                    <span class="badge badge-light-danger text-danger fw-bolder fs-9">{{ number_format($itemShortage, 0) }}</span>
                                                @else
                                                    <span class="badge badge-light-success text-success fs-9">0 (Lengkap)</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $it->status_badge['class'] }} fs-9">{{ $it->status_badge['label'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary Card Info -->
                        <div class="row g-2 mb-2">
                            <div class="col-sm-4">
                                <div class="p-3 bg-light rounded-3 text-center">
                                    <div class="fs-8 text-muted">Total Diminta</div>
                                    <div class="fs-6 fw-bolder text-gray-900">{{ number_format($detailTotalReq, 0) }} Pcs</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="p-3 bg-light-success rounded-3 text-center">
                                    <div class="fs-8 text-muted">Bisa Dikeluarkan (Stok)</div>
                                    <div class="fs-6 fw-bolder text-success">{{ number_format($detailCanIssue, 0) }} Pcs</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="p-3 {{ $detailShortage > 0 ? 'bg-light-danger' : 'bg-light-primary' }} rounded-3 text-center">
                                    <div class="fs-8 text-muted">Sisa Kekurangan (PR)</div>
                                    <div class="fs-6 fw-bolder {{ $detailShortage > 0 ? 'text-danger' : 'text-primary' }}">{{ number_format($detailShortage, 0) }} Pcs</div>
                                </div>
                            </div>
                        </div>

                        @if($selectedMol->purchaseRequests && $selectedMol->purchaseRequests->count() > 0)
                            <div class="p-3 bg-light-info rounded-3 mt-3">
                                <div class="fs-8 text-info fw-bold mb-1">
                                    <i class="ki-outline ki-document fs-6 text-info me-1"></i> Purchase Request Terkait:
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($selectedMol->purchaseRequests as $pr)
                                        <a href="{{ route('scm.pr') }}" class="badge badge-light-primary fs-8 fw-bold text-decoration-none">
                                            {{ $pr->pr_number }} (Status: {{ $pr->status }}) &rarr;
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer border-top py-3 px-6 d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="d-flex flex-wrap gap-2">
                            @if(in_array($selectedMol->status, ['submitted', 'draft', 'partially_issued', 'approved']))
                                @if($detailCanIssue > 0 && $detailShortage > 0)
                                    <!-- Smart 1-Click Action -->
                                    <button type="button" wire:click="approveAndGeneratePr('{{ $selectedMol->id }}')" class="btn btn-success btn-sm fs-7 fw-bold" title="Keluarkan {{ $detailCanIssue }} Pcs dari stok dan buat PR untuk sisa {{ $detailShortage }} Pcs">
                                        <i class="ki-outline ki-check-circle fs-4 me-1"></i> Approve ({{ $detailCanIssue }}) & Buat PR Sisa ({{ $detailShortage }})
                                    </button>

                                    <button type="button" wire:click="approveAndIssue('{{ $selectedMol->id }}')" class="btn btn-light-success btn-sm fs-7" title="Keluarkan stok yang tersedia saja">
                                        <i class="ki-outline ki-check fs-5 me-1"></i> Approve Stok Saja
                                    </button>

                                    <button type="button" wire:click="generateToPr('{{ $selectedMol->id }}')" class="btn btn-light-warning btn-sm fs-7" title="Buat PR untuk sisa kekurangan">
                                        <i class="ki-outline ki-document fs-5 me-1"></i> Buat PR Sisa
                                    </button>
                                @elseif($detailCanIssue > 0 && $detailShortage == 0)
                                    <button type="button" wire:click="approveAndIssue('{{ $selectedMol->id }}')" class="btn btn-success btn-sm fs-7 fw-bold">
                                        <i class="ki-outline ki-check-circle fs-4 me-1"></i> Approve & Keluarkan Seluruh Stok
                                    </button>
                                @elseif($detailShortage > 0)
                                    <button type="button" wire:click="generateToPr('{{ $selectedMol->id }}')" class="btn btn-warning btn-sm fs-7 fw-bold">
                                        <i class="ki-outline ki-document fs-4 me-1"></i> Buat PR Pengadaan ({{ $detailShortage }} Pcs)
                                    </button>
                                @endif
                            @endif

                            @if(in_array($selectedMol->status, ['submitted', 'draft']))
                                <button type="button" wire:click="deleteMol('{{ $selectedMol->id }}')" wire:confirm="Hapus permanen transaksi MOL ini?" class="btn btn-danger btn-sm fs-7 fw-bold">
                                    <i class="ki-outline ki-trash fs-5 me-1"></i> Hapus
                                </button>
                            @endif
                            @if(!in_array($selectedMol->status, ['cancelled', 'draft']))
                                <button type="button" wire:click="cancelMol('{{ $selectedMol->id }}')" wire:confirm="Batalkan transaksi MOL ini? Stok (jika sudah dikeluarkan) akan dikembalikan ke gudang." class="btn btn-danger btn-sm fs-7 fw-bold">
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
