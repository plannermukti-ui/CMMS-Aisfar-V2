<div class="d-flex flex-column flex-column-fluid">
    <!-- Toolbar -->
    <div class="d-flex flex-stack mb-6">
        <div>
            <h1 class="text-gray-900 fw-bold fs-2 mb-1">Component Tracker & Silsilah Komponen</h1>
            <span class="text-muted fs-7">Pelacakan jam operasi, riwayat rotasi antar-unit, dan umur komponen (Component Lifecycle).</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button wire:click="openCreateModal" class="btn btn-primary btn-sm fw-bold">
                <i class="ki-outline ki-plus-circle fs-4 me-1"></i> Tambah Master Komponen
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
                        <i class="ki-outline ki-cube-2 fs-2"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-gray-900">{{ $totalComponents }}</div>
                        <span class="fs-8 text-muted fw-semibold">Total Komponen Terdaftar</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle bg-light-success text-success me-3">
                        <i class="ki-outline ki-wrench fs-2"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-gray-900">{{ $installedCount }}</div>
                        <span class="fs-8 text-muted fw-semibold">Terpasang di Unit</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle bg-light-info text-info me-3">
                        <i class="ki-outline ki-shop fs-2"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-gray-900">{{ $readySpareCount }}</div>
                        <span class="fs-8 text-muted fw-semibold">Ready Spare (Workshop)</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle bg-light-warning text-warning me-3">
                        <i class="ki-outline ki-delivery-3 fs-2"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-gray-900">{{ $outsideRepairCount }}</div>
                        <span class="fs-8 text-muted fw-semibold">Di Bengkel Luar (OSR)</span>
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
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-sm form-control-solid ps-9 w-225px rounded-pill fs-7" placeholder="Cari kode, SN, nama..." />
                </div>
                <select wire:model.live="typeFilter" class="form-select form-select-sm form-select-solid w-150px rounded-pill fs-7">
                    <option value="">Semua Tipe</option>
                    <option value="engine">Engine</option>
                    <option value="transmission">Transmission</option>
                    <option value="differential">Differential</option>
                    <option value="hydraulic_pump">Hydraulic Pump</option>
                    <option value="final_drive">Final Drive</option>
                    <option value="undercarriage">Undercarriage</option>
                    <option value="cylinder">Cylinder</option>
                    <option value="other">Lainnya</option>
                </select>
                <select wire:model.live="statusFilter" class="form-select form-select-sm form-select-solid w-175px rounded-pill fs-7">
                    <option value="">Semua Status</option>
                    <option value="installed">Terpasang di Unit</option>
                    <option value="ready_spare">Ready Spare</option>
                    <option value="in_repair_workshop">Perbaikan Internal</option>
                    <option value="in_outside_repair">Bengkel Luar (OSR)</option>
                    <option value="scrapped">Scrapped (Afkir)</option>
                </select>
            </div>
        </div>

        <!-- Card Body Table -->
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-3 fs-7">
                    <thead>
                        <tr class="fw-bolder text-muted bg-light">
                            <th class="ps-3 rounded-start">Kode & Komponen</th>
                            <th>Tipe & Brand/Model</th>
                            <th>Serial Number</th>
                            <th>Posisi Terpasang</th>
                            <th>Umur & Jam Kerja (Life Meter)</th>
                            <th>Status</th>
                            <th class="text-end pe-3 rounded-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($components as $comp)
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-35px symbol-circle bg-light-primary text-primary me-3">
                                            <i class="ki-outline ki-cube-2 fs-4"></i>
                                        </div>
                                        <div>
                                            <a href="javascript:void(0)" wire:click="openDetailModal('{{ $comp->id }}')" class="text-gray-900 fw-bold text-hover-primary fs-7 d-block">
                                                {{ $comp->component_code }}
                                            </a>
                                            <span class="text-muted fs-8">{{ $comp->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light-dark fs-8 fw-semibold text-capitalize">{{ str_replace('_', ' ', $comp->component_type) }}</span>
                                    <span class="text-muted fs-8 d-block">{{ $comp->brand_model ?: '-' }}</span>
                                </td>
                                <td>
                                    <span class="text-gray-800 fw-bold">{{ $comp->serial_number ?: '-' }}</span>
                                </td>
                                <td>
                                    @if($comp->equipment)
                                        <span class="badge badge-light-success fs-8 fw-bold">
                                            <i class="ki-outline ki-truck text-success fs-7 me-1"></i> {{ $comp->equipment->unit }}
                                        </span>
                                        <span class="text-muted fs-8 d-block">{{ $comp->position ?: 'Terpasang' }}</span>
                                    @else
                                        <span class="badge badge-light-secondary text-gray-700 fs-8">
                                            <i class="ki-outline ki-shop fs-7 me-1"></i> {{ $comp->position ?: 'Gudang / Workshop' }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column w-150px">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-gray-800 fw-bold fs-8">{{ number_format($comp->accumulated_hours, 0) }} / {{ number_format($comp->target_life_hours, 0) }} Jam</span>
                                            <span class="fw-bold fs-8 {{ $comp->life_percentage >= 90 ? 'text-danger' : ($comp->life_percentage >= 70 ? 'text-warning' : 'text-success') }}">{{ $comp->life_percentage }}%</span>
                                        </div>
                                        <div class="progress h-6px bg-light-secondary rounded-pill">
                                            <div class="progress-bar rounded-pill {{ $comp->life_percentage >= 90 ? 'bg-danger' : ($comp->life_percentage >= 70 ? 'bg-warning' : 'bg-success') }}" style="width: {{ min(100, $comp->life_percentage) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $comp->status_badge['class'] }} fs-8 fw-bold">{{ $comp->status_badge['label'] }}</span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button wire:click="openDetailModal('{{ $comp->id }}')" class="btn btn-icon btn-sm btn-light-info rounded-circle" title="Detail & Riwayat Silsilah">
                                            <i class="ki-outline ki-eye fs-5"></i>
                                        </button>
                                        <button type="button" onclick="window.open('{{ route('plt.components.print', $comp->id) }}', '_blank')" class="btn btn-icon btn-sm btn-light-success rounded-circle" title="Cetak Komponen">
                                            <i class="ki-outline ki-printer fs-5"></i>
                                        </button>
                                        <button wire:click="openTransferModal('{{ $comp->id }}')" class="btn btn-icon btn-sm btn-light-success rounded-circle" title="Rotasi / Pasang / Lepas Komponen">
                                            <i class="ki-outline ki-arrow-up-down fs-5"></i>
                                        </button>
                                        <button wire:click="openEditModal('{{ $comp->id }}')" class="btn btn-icon btn-sm btn-light-primary rounded-circle" title="Edit Master Komponen">
                                            <i class="ki-outline ki-pencil fs-5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-10 text-muted">
                                    <i class="ki-outline ki-cube-2 fs-3x text-muted mb-2"></i>
                                    <div class="fs-6 fw-bold">Belum Ada Komponen Terdaftar</div>
                                    <span class="fs-7">Tambahkan master komponen pertama untuk melacak silsilah & rotasi unit.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-end mt-4">
                {{ $components->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Form (Create / Edit) -->
    @if($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header pb-0 border-0 justify-content-between">
                        <h3 class="fw-bold text-gray-900">{{ $selectedComponentId ? 'Edit Komponen' : 'Tambah Master Komponen Baru' }}</h3>
                        <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-2"></i>
                        </button>
                    </div>
                    <form wire:submit.prevent="saveComponent">
                        <div class="modal-body py-5">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label required fs-7 fw-bold">Nama Komponen</label>
                                    <input type="text" wire:model="name" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Contoh: Engine Diesel CAT C15" required />
                                    @error('name') <span class="text-danger fs-8">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fs-7 fw-bold">Tipe Komponen</label>
                                    <select wire:model="component_type" class="form-select form-select-sm form-select-solid rounded-2">
                                        <option value="engine">Engine (Mesin)</option>
                                        <option value="transmission">Transmission (Transmisi)</option>
                                        <option value="differential">Differential / Gardan</option>
                                        <option value="hydraulic_pump">Hydraulic Pump (Pompa Hidrolik)</option>
                                        <option value="final_drive">Final Drive</option>
                                        <option value="undercarriage">Undercarriage (Track/Roller)</option>
                                        <option value="cylinder">Hydraulic Cylinder</option>
                                        <option value="other">Komponen Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Serial Number (S/N)</label>
                                    <input type="text" wire:model="serial_number" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Nomor Seri Fisik Komponen" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Brand & Model / Part Number</label>
                                    <input type="text" wire:model="brand_model" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Contoh: Caterpillar 3406 / P/N 123-4567" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fs-7 fw-bold">Target Umur Komponen (Jam Target Life)</label>
                                    <input type="number" step="100" wire:model="target_life_hours" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Contoh: 12000" required />
                                    @error('target_life_hours') <span class="text-danger fs-8">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fs-7 fw-bold">Akumulasi Jam Kerja Saat Ini (Jam)</label>
                                    <input type="number" step="0.1" wire:model="accumulated_hours" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Contoh: 4500" required />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Unit Terpasang Saat Ini</label>
                                    <select wire:model="equipment_id" class="form-select form-select-sm form-select-solid rounded-2">
                                        <option value="">-- Belum Terpasang (Ready di Workshop) --</option>
                                        @foreach($equipments as $eq)
                                            <option value="{{ $eq->id }}">{{ $eq->unit }} ({{ $eq->reffEquip?->model ?? 'Unit' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Posisi Pemasangan</label>
                                    <input type="text" wire:model="position" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Contoh: Depan Kiri, Main Engine, Rear Axle" />
                                </div>
                                <div class="col-12">
                                    <label class="form-label fs-7 fw-bold">Catatan Tambahan / Riwayat Awal</label>
                                    <textarea wire:model="remarks" rows="2" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Keterangan kondisi komponen, asal usul overhaul..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-sm btn-light">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary fw-bold">
                                <i class="ki-outline ki-check fs-4 me-1"></i> Simpan Data Komponen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Transfer / Movement -->
    @if($showTransferModal && $activeComponent)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header pb-0 border-0 justify-content-between">
                        <div>
                            <h4 class="fw-bold text-gray-900 mb-0">Catat Rotasi / Pergerakan Komponen</h4>
                            <span class="fs-8 text-muted">{{ $activeComponent->component_code }} - {{ $activeComponent->name }}</span>
                        </div>
                        <button type="button" wire:click="$set('showTransferModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-2"></i>
                        </button>
                    </div>
                    <form wire:submit.prevent="processTransfer">
                        <div class="modal-body py-4">
                            <div class="alert alert-light-primary p-3 mb-4 rounded-3 fs-8">
                                <strong>Status Saat Ini:</strong> {{ $activeComponent->status_badge['label'] }}
                                @if($activeComponent->equipment)
                                    (Terpasang di {{ $activeComponent->equipment->unit }})
                                @endif
                            </div>
                            <div class="mb-3">
                                <label class="form-label required fs-7 fw-bold">Jenis Pergerakan</label>
                                <select wire:model.live="movement_type" class="form-select form-select-sm form-select-solid rounded-2" required>
                                    <option value="install">Pasang ke Unit Baru (Install on Unit)</option>
                                    <option value="remove">Lepas dari Unit (Dismantle ke Workshop)</option>
                                    <option value="dispatch_outside">Kirim ke Bengkel Luar / Vendor (OSR)</option>
                                    <option value="receive_outside">Terima Kembali dari Bengkel Luar (Received OSR)</option>
                                    <option value="scrap">Afkir / Rusak Total (Scrap)</option>
                                </select>
                            </div>
                            @if($movement_type === 'install')
                                <div class="mb-3">
                                    <label class="form-label required fs-7 fw-bold">Pilih Unit Tujuan</label>
                                    <select wire:model="target_equipment_id" class="form-select form-select-sm form-select-solid rounded-2" required>
                                        <option value="">-- Pilih Unit --</option>
                                        @foreach($equipments as $eq)
                                            <option value="{{ $eq->id }}">{{ $eq->unit }} ({{ $eq->reffEquip?->model ?? '' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fs-7 fw-bold">Posisi Terpasang</label>
                                    <input type="text" wire:model="transfer_position" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Contoh: Engine Compartment, Front Axle" />
                                </div>
                            @endif
                            <div class="mb-3">
                                <label class="form-label fs-7 fw-bold">Hourmeter Unit Saat Transaksi (HM)</label>
                                <input type="number" step="0.1" wire:model="equipment_hm" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Contoh: 12500.5" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label fs-7 fw-bold">Catatan / Alasan Pergerakan</label>
                                <textarea wire:model="transfer_notes" rows="2" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Contoh: Komponen di-overhaul dan dipasang menggantikan unit rusak..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" wire:click="$set('showTransferModal', false)" class="btn btn-sm btn-light">Batal</button>
                            <button type="submit" class="btn btn-sm btn-success fw-bold">
                                <i class="ki-outline ki-check fs-4 me-1"></i> Simpan Transaksi Pergerakan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Detail & History -->
    @if($showDetailModal && $activeComponent)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header pb-0 border-0 justify-content-between">
                        <div>
                            <span class="badge {{ $activeComponent->status_badge['class'] }} mb-1">{{ $activeComponent->status_badge['label'] }}</span>
                            <h3 class="fw-bold text-gray-900 mb-0">{{ $activeComponent->component_code }} - {{ $activeComponent->name }}</h3>
                            <span class="fs-8 text-muted">S/N: {{ $activeComponent->serial_number ?: 'N/A' }} | Brand/Model: {{ $activeComponent->brand_model ?: '-' }}</span>
                        </div>
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-2"></i>
                        </button>
                    </div>
                    <div class="modal-body py-5">
                        <!-- Life Meter Card -->
                        <div class="card bg-light-primary border-0 p-4 mb-6 rounded-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fs-7 fw-bold text-gray-900">
                                    Total Jam Kerja Berjalan: <span class="text-primary">{{ number_format($activeComponent->accumulated_hours, 1) }} Jam</span>
                                </div>
                                <div class="fs-7 fw-bold text-gray-700">
                                    Target Life: <span class="text-dark">{{ number_format($activeComponent->target_life_hours, 0) }} Jam</span> ({{ $activeComponent->life_percentage }}%)
                                </div>
                            </div>
                            <div class="progress h-8px bg-white rounded-pill">
                                <div class="progress-bar rounded-pill {{ $activeComponent->life_percentage >= 90 ? 'bg-danger' : ($activeComponent->life_percentage >= 70 ? 'bg-warning' : 'bg-primary') }}" style="width: {{ min(100, $activeComponent->life_percentage) }}%"></div>
                            </div>
                        </div>

                        <!-- History Tabs -->
                        <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active fw-bold text-active-primary" data-bs-toggle="tab" href="#kt_tab_movements">
                                    <i class="ki-outline ki-arrow-up-down fs-5 me-1"></i> Silsilah & Rotasi ({{ $activeComponent->movements->count() }})
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link fw-bold text-active-primary" data-bs-toggle="tab" href="#kt_tab_ccr">
                                    <i class="ki-outline ki-clipboard fs-5 me-1"></i> Kondisi Fisik CCR ({{ $activeComponent->conditionReports->count() }})
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link fw-bold text-active-primary" data-bs-toggle="tab" href="#kt_tab_osr">
                                    <i class="ki-outline ki-delivery-3 fs-5 me-1"></i> Perbaikan Luar OSR ({{ $activeComponent->outsideRepairs->count() }})
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Tab Movements -->
                            <div class="tab-pane fade show active" id="kt_tab_movements" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-3 fs-8">
                                        <thead>
                                            <tr class="fw-bolder text-muted bg-light">
                                                <th class="ps-2">Waktu Rotasi</th>
                                                <th>Jenis Pergerakan</th>
                                                <th>Dari Posisi</th>
                                                <th>Ke Posisi</th>
                                                <th>HM Unit</th>
                                                <th>Mekanik / Eksekutor</th>
                                                <th>Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($activeComponent->movements as $mov)
                                                <tr>
                                                    <td class="ps-2 fw-semibold text-gray-800">{{ $mov->movement_date->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        <span class="badge badge-light-primary fw-bold text-capitalize">{{ str_replace('_', ' ', $mov->movement_type) }}</span>
                                                    </td>
                                                    <td>{{ $mov->fromEquipment?->unit ?: 'Gudang/Workshop' }}</td>
                                                    <td>{{ $mov->toEquipment?->unit ?: 'Gudang/Workshop' }}</td>
                                                    <td>{{ $mov->equipment_hm ? number_format($mov->equipment_hm, 1) : '-' }}</td>
                                                    <td>{{ $mov->performer?->full_name ?: '-' }}</td>
                                                    <td>{{ $mov->notes ?: '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center py-5 text-muted">Belum ada riwayat pergerakan.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab CCR -->
                            <div class="tab-pane fade" id="kt_tab_ccr" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-3 fs-8">
                                        <thead>
                                            <tr class="fw-bolder text-muted bg-light">
                                                <th class="ps-2">No. CCR</th>
                                                <th>Tanggal</th>
                                                <th>Keausan (Wear %)</th>
                                                <th>Kondisi Fisik</th>
                                                <th>Rekomendasi</th>
                                                <th>Inspektor</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($activeComponent->conditionReports as $ccr)
                                                <tr>
                                                    <td class="ps-2 fw-bold text-primary">{{ $ccr->ccr_number }}</td>
                                                    <td>{{ $ccr->ccr_date->format('d/m/Y') }}</td>
                                                    <td><span class="fw-bold {{ $ccr->wear_percentage >= 80 ? 'text-danger' : 'text-gray-800' }}">{{ $ccr->wear_percentage }}%</span></td>
                                                    <td><span class="badge badge-light-secondary text-capitalize">{{ str_replace('_', ' ', $ccr->physical_condition) }}</span></td>
                                                    <td><span class="badge {{ $ccr->recommendation_badge['class'] }}">{{ $ccr->recommendation_badge['label'] }}</span></td>
                                                    <td>{{ $ccr->inspector?->full_name ?: '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-5 text-muted">Belum ada laporan CCR untuk komponen ini.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Tab OSR -->
                            <div class="tab-pane fade" id="kt_tab_osr" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-3 fs-8">
                                        <thead>
                                            <tr class="fw-bolder text-muted bg-light">
                                                <th class="ps-2">No. OSR</th>
                                                <th>Tanggal Order</th>
                                                <th>Vendor Rekanan</th>
                                                <th>Pekerjaan (Scope)</th>
                                                <th>Biaya Aktual</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($activeComponent->outsideRepairs as $osr)
                                                <tr>
                                                    <td class="ps-2 fw-bold text-primary">{{ $osr->osr_number }}</td>
                                                    <td>{{ $osr->order_date->format('d/m/Y') }}</td>
                                                    <td class="fw-bold">{{ $osr->vendor?->name ?: '-' }}</td>
                                                    <td>{{ $osr->scope_of_work ?: $osr->item_description }}</td>
                                                    <td>Rp {{ number_format($osr->actual_cost ?: $osr->estimated_cost, 0, ',', '.') }}</td>
                                                    <td><span class="badge {{ $osr->status_badge['class'] }}">{{ $osr->status_badge['label'] }}</span></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-5 text-muted">Belum ada riwayat perbaikan luar OSR.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-sm btn-light">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
