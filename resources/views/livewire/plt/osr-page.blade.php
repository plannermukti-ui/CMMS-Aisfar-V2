<div class="d-flex flex-column flex-column-fluid">
    <!-- Toolbar -->
    <div class="d-flex flex-stack mb-6">
        <div>
            <h1 class="text-gray-900 fw-bold fs-2 mb-1">OSR - Outside Repair Order (Pekerjaan Luar Bengkel)</h1>
            <span class="text-muted fs-7">Manajemen pekerjaan bubut/spesialis vendor luar (Machine Shop, Hardchroming, Line Boring, Rewinding, Dyno Test).</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button wire:click="openCreateModal" class="btn btn-primary btn-sm fw-bold">
                <i class="ki-outline ki-plus-circle fs-4 me-1"></i> Buat Order Luar (OSR)
            </button>
        </div>
    </div>

    <!-- Flash Alert -->
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center p-4 mb-6 rounded-3 shadow-xs">
            <i class="ki-outline ki-check-circle fs-2 text-success me-3"></i>
            <div class="text-success fw-semibold">{{ session('success') }}</div>
        </div>
    @endif

    <!-- KPI Metric Cards -->
    <div class="row g-4 mb-6">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle bg-light-primary text-primary me-3">
                        <i class="ki-outline ki-delivery-3 fs-2"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-gray-900">{{ $totalOsr }}</div>
                        <span class="fs-8 text-muted fw-semibold">Total Order Pekerjaan Luar</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle bg-light-warning text-warning me-3">
                        <i class="ki-outline ki-time fs-2"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-gray-900">{{ $inProgressCount }}</div>
                        <span class="fs-8 text-muted fw-semibold">Sedang Dikerjakan di Vendor</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle bg-light-success text-success me-3">
                        <i class="ki-outline ki-check-circle fs-2"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-gray-900">{{ $receivedCount }}</div>
                        <span class="fs-8 text-muted fw-semibold">Tiba di Site (Ready Pasang)</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle bg-light-info text-info me-3">
                        <i class="ki-outline ki-wallet fs-2"></i>
                    </div>
                    <div>
                        <div class="fs-5 fw-bolder text-gray-900">Rp {{ number_format($totalCost, 0, ',', '.') }}</div>
                        <span class="fs-8 text-muted fw-semibold">Total Biaya Perbaikan Luar</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card card-flush shadow-xs border border-gray-200">
        <!-- Card Header & Filters -->
        <div class="card-header border-0 pt-5">
            <div class="card-title d-flex align-items-center gap-3 flex-wrap">
                <div class="position-relative">
                    <i class="ki-outline ki-magnifier fs-4 position-absolute top-50 translate-middle-y ms-3 text-gray-500"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-sm form-control-solid ps-9 w-225px rounded-pill fs-7" placeholder="Cari No. OSR, deskripsi, SJ..." />
                </div>
                <select wire:model.live="vendorFilter" class="form-select form-select-sm form-select-solid w-175px rounded-pill fs-7">
                    <option value="">Semua Vendor Rekanan</option>
                    @foreach($vendors as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="statusFilter" class="form-select form-select-sm form-select-solid w-175px rounded-pill fs-7">
                    <option value="">Semua Status</option>
                    <option value="dispatched">Terkirim ke Vendor</option>
                    <option value="vendor_inspecting">Inspeksi Vendor</option>
                    <option value="quotation_approved">Quotation Disetujui</option>
                    <option value="in_progress">Pengerjaan / Machining</option>
                    <option value="testing_qc">Uji QC / Test Bench</option>
                    <option value="received_at_site">Tiba di Site</option>
                    <option value="closed">Selesai Bergaransi</option>
                </select>
            </div>
        </div>

        <!-- Card Body Table -->
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-3 fs-7">
                    <thead>
                        <tr class="fw-bolder text-muted bg-light">
                            <th class="ps-3 rounded-start">No. OSR & Tanggal</th>
                            <th>Vendor Rekanan</th>
                            <th>Deskripsi Komponen / Jasa</th>
                            <th>Target Selesai & Lead Time</th>
                            <th>Estimasi / Aktual Biaya</th>
                            <th>Garansi Vendor</th>
                            <th>Status Order</th>
                            <th class="text-end pe-3 rounded-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $osr)
                            <tr>
                                <td class="ps-3">
                                    <a href="javascript:void(0)" wire:click="openDetailModal('{{ $osr->id }}')" class="text-primary fw-bold fs-7 d-block">
                                        {{ $osr->osr_number }}
                                    </a>
                                    <span class="text-muted fs-8">{{ $osr->order_date->format('d/m/Y') }}</span>
                                </td>
                                <td>
                                    <span class="text-gray-900 fw-bold fs-7 d-block">{{ $osr->vendor->name ?? 'Vendor N/A' }}</span>
                                    <span class="text-muted fs-8">{{ $osr->vendor->phone ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="text-gray-900 fw-bold fs-7 d-block">{{ $osr->item_description }}</span>
                                    <span class="text-muted fs-8 text-truncate d-inline-block" style="max-width: 180px;">{{ $osr->scope_of_work }}</span>
                                </td>
                                <td>
                                    @if($osr->estimated_completion_date)
                                        <span class="text-gray-800 fw-semibold fs-8 d-block">Target: {{ $osr->estimated_completion_date->format('d/m/Y') }}</span>
                                        @if($osr->dispatch_date)
                                            <span class="text-muted fs-8">Kirim: {{ $osr->dispatch_date->format('d/m/Y') }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted fs-8">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-gray-900 fw-bold fs-7 d-block">Rp {{ number_format($osr->actual_cost ?: $osr->estimated_cost, 0, ',', '.') }}</span>
                                    @if($osr->actual_cost && $osr->estimated_cost)
                                        <span class="text-muted fs-8">Est: Rp {{ number_format($osr->estimated_cost, 0, ',', '.') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-light-dark fs-8 fw-semibold">{{ $osr->warranty_period_months }} Bln / {{ number_format($osr->warranty_period_hours, 0) }} Jam</span>
                                </td>
                                <td>
                                    <span class="badge {{ $osr->status_badge['class'] }} fs-8 fw-bold">{{ $osr->status_badge['label'] }}</span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-flex justify-content-end gap-1">
                                        @if(!in_array($osr->status, ['received_at_site', 'closed', 'rejected_warranty']))
                                            <button wire:click="openQcModal('{{ $osr->id }}')" class="btn btn-icon btn-sm btn-light-success rounded-circle" title="Terima Barang & QC Check">
                                                <i class="ki-outline ki-check-circle fs-5"></i>
                                            </button>
                                        @endif
                                        <button wire:click="openDetailModal('{{ $osr->id }}')" class="btn btn-icon btn-sm btn-light-info rounded-circle" title="Detail Order OSR">
                                            <i class="ki-outline ki-eye fs-5"></i>
                                        </button>
                                        <button wire:click="openEditModal('{{ $osr->id }}')" class="btn btn-icon btn-sm btn-light-primary rounded-circle" title="Edit Order OSR">
                                            <i class="ki-outline ki-pencil fs-5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10 text-muted">
                                    <i class="ki-outline ki-delivery-3 fs-3x text-muted mb-2"></i>
                                    <div class="fs-6 fw-bold">Belum Ada Order Pekerjaan Luar (OSR)</div>
                                    <span class="fs-7">Buat order perbaikan luar untuk melacak vendor bubut, komponen dikirim, biaya, dan garansi.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-end mt-4">
                {{ $orders->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Form (Create / Edit OSR) -->
    @if($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header pb-0 border-0 justify-content-between">
                        <h3 class="fw-bold text-gray-900">{{ $selectedOsrId ? 'Edit Order Perbaikan Luar' : 'Buat Order Perbaikan Luar Bengkel (OSR)' }}</h3>
                        <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-2"></i>
                        </button>
                    </div>
                    <form wire:submit.prevent="saveOsr">
                        <div class="modal-body py-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required fs-7 fw-bold">Tanggal Order OSR</label>
                                    <input type="date" wire:model="order_date" class="form-control form-control-sm form-control-solid rounded-2" required />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fs-7 fw-bold">Pilih Vendor Rekanan</label>
                                    <select wire:model="vendor_id" class="form-select form-select-sm form-select-solid rounded-2" required>
                                        <option value="">-- Pilih Vendor Spesialis / Bengkel Bubut --</option>
                                        @foreach($vendors as $v)
                                            <option value="{{ $v->id }}">{{ $v->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('vendor_id') <span class="text-danger fs-8">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Pilih Dari Master Komponen</label>
                                    <select wire:model.live="component_id" class="form-select form-select-sm form-select-solid rounded-2">
                                        <option value="">-- Jasa Umum / Part Lepasan --</option>
                                        @foreach($components as $c)
                                            <option value="{{ $c->id }}">{{ $c->component_code }} - {{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Unit Terkait (Opsional)</label>
                                    <select wire:model="equipment_id" class="form-select form-select-sm form-select-solid rounded-2">
                                        <option value="">-- Tanpa Unit Khusus --</option>
                                        @foreach($equipments as $eq)
                                            <option value="{{ $eq->id }}">{{ $eq->unit }} ({{ $eq->reffEquip->model ?? '' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label required fs-7 fw-bold">Deskripsi Komponen / Barang Yang Dikirim</label>
                                    <input type="text" wire:model="item_description" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Contoh: Hydraulic Cylinder Boom Left, Line Boring Center Joint" required />
                                    @error('item_description') <span class="text-danger fs-8">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label required fs-7 fw-bold">Detail Lingkup Pekerjaan (Scope of Work)</label>
                                    <textarea wire:model="scope_of_work" rows="3" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Uraikan detail pekerjaan yang diminta ke vendor: Hardchrome rod, ganti seal kit komplit, uji test bench tekanan 250 bar..." required></textarea>
                                    @error('scope_of_work') <span class="text-danger fs-8">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Alasan Dikerjakan di Luar (Reason)</label>
                                    <select wire:model="reason_for_outside" class="form-select form-select-sm form-select-solid rounded-2">
                                        <option value="lack_of_machining_equipment">Butuh Mesin Khusus (Bubut/Honing/Hardchrome)</option>
                                        <option value="lack_of_specialized_tool">Ketiadaan Special Tool / Dyno Test</option>
                                        <option value="overload_workshop">Kapasitas Workshop Internal Penuh</option>
                                        <option value="warranty_claim">Klaim Garansi Distributor / Vendor</option>
                                        <option value="specialized_calibration">Kalibrasi & Sertifikasi Khusus</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">No. Surat Jalan Kirim Keluar</label>
                                    <input type="text" wire:model="delivery_letter_number" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Contoh: SJ-OUT-2608-012" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Tanggal Kirim ke Vendor</label>
                                    <input type="date" wire:model="dispatch_date" class="form-control form-control-sm form-control-solid rounded-2" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Target Tanggal Selesai</label>
                                    <input type="date" wire:model="estimated_completion_date" class="form-control form-control-sm form-control-solid rounded-2" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Estimasi Biaya (Rp)</label>
                                    <input type="number" step="10000" wire:model="estimated_cost" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Contoh: 18500000" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Biaya Aktual Final (Rp)</label>
                                    <input type="number" step="10000" wire:model="actual_cost" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Jika sudah ada invoice/quotation final" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-7 fw-bold">Masa Garansi (Bulan)</label>
                                    <input type="number" wire:model="warranty_period_months" class="form-control form-control-sm form-control-solid rounded-2" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-7 fw-bold">Masa Garansi (Jam Operasi)</label>
                                    <input type="number" wire:model="warranty_period_hours" class="form-control form-control-sm form-control-solid rounded-2" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-7 fw-bold">Status Order</label>
                                    <select wire:model="status" class="form-select form-select-sm form-select-solid rounded-2">
                                        <option value="dispatched">Terkirim ke Vendor</option>
                                        <option value="vendor_inspecting">Inspeksi Vendor</option>
                                        <option value="quotation_approved">Quotation Disetujui</option>
                                        <option value="in_progress">Sedang Dikerjakan</option>
                                        <option value="testing_qc">Uji QC / Test Bench</option>
                                        <option value="received_at_site">Tiba di Site</option>
                                        <option value="closed">Selesai Bergaransi</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-sm btn-light">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary fw-bold">
                                <i class="ki-outline ki-check fs-4 me-1"></i> Simpan Order OSR
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal QC & Receive -->
    @if($showQcModal && $activeOsr)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header pb-0 border-0 justify-content-between">
                        <div>
                            <h4 class="fw-bold text-gray-900 mb-0">Penerimaan Komponen & Quality Check</h4>
                            <span class="fs-8 text-muted">{{ $activeOsr->osr_number }} - {{ $activeOsr->item_description }}</span>
                        </div>
                        <button type="button" wire:click="$set('showQcModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-2"></i>
                        </button>
                    </div>
                    <form wire:submit.prevent="submitQcReceive">
                        <div class="modal-body py-4">
                            <div class="mb-3">
                                <label class="form-label required fs-7 fw-bold">Status Hasil Inspeksi QC</label>
                                <select wire:model="qc_passed" class="form-select form-select-sm form-select-solid rounded-2" required>
                                    <option value="1">Lolos QC (QC Passed - Kondisi Sempurna & Sesuai Scope)</option>
                                    <option value="0">Reject QC / Klaim Garansi Vendor (Ada Cacat/Bocor)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fs-7 fw-bold">No. Surat Jalan Terima dari Vendor</label>
                                <input type="text" wire:model="received_letter_number" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Contoh: SJ-RCV-2608-055" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label fs-7 fw-bold">Catatan Hasil Pemeriksaan Fisik / Test Bench</label>
                                <textarea wire:model="qc_notes" rows="3" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Uraikan kondisi visual, hasil pengukuran dimensi, sertifikat garansi terlampir..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" wire:click="$set('showQcModal', false)" class="btn btn-sm btn-light">Batal</button>
                            <button type="submit" class="btn btn-sm btn-success fw-bold">
                                <i class="ki-outline ki-check fs-4 me-1"></i> Konfirmasi Terima di Site
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Detail OSR -->
    @if($showDetailModal && $activeOsr)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header pb-0 border-0 justify-content-between">
                        <div>
                            <span class="badge {{ $activeOsr->status_badge['class'] }} mb-1">{{ $activeOsr->status_badge['label'] }}</span>
                            <h3 class="fw-bold text-gray-900 mb-0">Order OSR: {{ $activeOsr->osr_number }}</h3>
                            <span class="fs-8 text-muted">Tanggal Order: {{ $activeOsr->order_date->format('d F Y') }}</span>
                        </div>
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-2"></i>
                        </button>
                    </div>
                    <div class="modal-body py-5">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3">
                                    <span class="fs-8 text-muted d-block fw-semibold">Vendor Rekanan</span>
                                    <span class="fs-6 fw-bold text-gray-900">{{ $activeOsr->vendor->name ?? 'N/A' }}</span>
                                    <span class="fs-8 text-muted d-block">{{ $activeOsr->vendor->address ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3">
                                    <span class="fs-8 text-muted d-block fw-semibold">Biaya Perbaikan</span>
                                    <span class="fs-5 fw-bolder text-primary">Rp {{ number_format($activeOsr->actual_cost ?: $activeOsr->estimated_cost, 0, ',', '.') }}</span>
                                    <span class="fs-8 text-muted d-block">Garansi: {{ $activeOsr->warranty_period_months }} Bulan / {{ number_format($activeOsr->warranty_period_hours, 0) }} Jam</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold text-gray-900 mb-1">Deskripsi Item & Lingkup Pekerjaan</h6>
                            <div class="p-3 bg-light rounded-3 text-gray-800 fs-7 mb-2">
                                <strong>{{ $activeOsr->item_description }}</strong>
                            </div>
                            <div class="p-3 bg-light-primary rounded-3 text-gray-800 fs-7">
                                {{ $activeOsr->scope_of_work }}
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="border border-gray-200 p-3 rounded-2">
                                    <span class="fs-8 text-muted d-block">Surat Jalan Kirim (Dispatch)</span>
                                    <span class="fs-7 fw-bold text-gray-900">{{ $activeOsr->delivery_letter_number ?: '-' }} ({{ $activeOsr->dispatch_date ? $activeOsr->dispatch_date->format('d/m/Y') : '-' }})</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border border-gray-200 p-3 rounded-2">
                                    <span class="fs-8 text-muted d-block">Surat Jalan Terima (Received)</span>
                                    <span class="fs-7 fw-bold text-gray-900">{{ $activeOsr->received_letter_number ?: '-' }} ({{ $activeOsr->actual_completion_date ? $activeOsr->actual_completion_date->format('d/m/Y') : '-' }})</span>
                                </div>
                            </div>
                        </div>

                        @if($activeOsr->qc_notes)
                            <div class="p-3 bg-light-success rounded-3">
                                <span class="fs-8 text-success fw-bold d-block">Catatan Quality Check:</span>
                                <span class="fs-7 text-gray-800">{{ $activeOsr->qc_notes }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-sm btn-light">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
