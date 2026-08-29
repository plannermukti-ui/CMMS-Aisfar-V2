<div class="d-flex flex-column flex-column-fluid">
    <!-- Toolbar -->
    <div class="d-flex flex-stack mb-6">
        <div>
            <h1 class="text-gray-900 fw-bold fs-2 mb-1">FAR - Failure Analysis Report & Investigasi Kerusakan</h1>
            <span class="text-muted fs-7">Laporan investigasi kegagalan teknis, analisis akar masalah (5-Why & Fishbone), dan tindakan korektif/preventif (CAPA).</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button wire:click="openCreateModal" class="btn btn-danger btn-sm fw-bold">
                <i class="ki-outline ki-plus-circle fs-4 me-1"></i> Buat Laporan Investigasi (FAR)
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
                    <div class="symbol symbol-45px symbol-circle bg-light-danger text-danger me-3">
                        <i class="ki-outline ki-shield-cross fs-2"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-gray-900">{{ $totalFar }}</div>
                        <span class="fs-8 text-muted fw-semibold">Total Kasus Investigasi (FAR)</span>
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
                        <div class="fs-4 fw-bolder text-gray-900">{{ $prematureCount }}</div>
                        <span class="fs-8 text-muted fw-semibold">Premature Failure (Kerusakan Dini)</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle bg-light-danger text-danger me-3">
                        <i class="ki-outline ki-cross-circle fs-2"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-gray-900">{{ $catastrophicCount }}</div>
                        <span class="fs-8 text-muted fw-semibold">Catastrophic Breakdown</span>
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
                        <div class="fs-4 fw-bolder text-gray-900">{{ $closedCount }}</div>
                        <span class="fs-8 text-muted fw-semibold">Investigasi Selesai (Closed)</span>
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
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-sm form-control-solid ps-9 w-225px rounded-pill fs-7" placeholder="Cari No. FAR, judul, unit..." />
                </div>
                <select wire:model.live="typeFilter" class="form-select form-select-sm form-select-solid w-175px rounded-pill fs-7">
                    <option value="">Semua Tipe Kerusakan</option>
                    <option value="premature_failure">Premature Failure</option>
                    <option value="catastrophic_breakdown">Catastrophic Breakdown</option>
                    <option value="fatigue_fracture">Fatigue Fracture (Patah)</option>
                    <option value="lubrication_failure">Lubrication Starvation</option>
                    <option value="overheating">Thermal Overheating</option>
                    <option value="operational_misuse">Operational Misuse</option>
                    <option value="assembly_error">Assembly / Install Error</option>
                </select>
                <select wire:model.live="statusFilter" class="form-select form-select-sm form-select-solid w-175px rounded-pill fs-7">
                    <option value="">Semua Status</option>
                    <option value="under_investigation">Dalam Investigasi</option>
                    <option value="review_manager">Review Plant Manager</option>
                    <option value="closed">Closed & Verified</option>
                </select>
            </div>
        </div>

        <!-- Card Body Table -->
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-3 fs-7">
                    <thead>
                        <tr class="fw-bolder text-muted bg-light">
                            <th class="ps-3 rounded-start">No. FAR & Tanggal</th>
                            <th>Unit & Komponen</th>
                            <th>Judul Kasus Kerusakan</th>
                            <th>Tipe Kerusakan</th>
                            <th>Akar Penyebab Utama (Root Cause)</th>
                            <th>Downtime & Biaya</th>
                            <th>Status</th>
                            <th class="text-end pe-3 rounded-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $far)
                            <tr>
                                <td class="ps-3">
                                    <a href="javascript:void(0)" wire:click="openDetailModal('{{ $far->id }}')" class="text-danger fw-bold fs-7 d-block">
                                        {{ $far->far_number }}
                                    </a>
                                    <span class="text-muted fs-8">{{ $far->incident_date->format('d/m/Y') }}</span>
                                </td>
                                <td>
                                    <span class="text-gray-900 fw-bold fs-7">{{ $far->equipment->unit ?? 'Unit N/A' }}</span>
                                    <span class="text-muted fs-8 d-block">{{ $far->component ? $far->component->name : ($far->unit_hm_at_failure ? number_format($far->unit_hm_at_failure, 1).' HM' : '-') }}</span>
                                </td>
                                <td>
                                    <span class="text-gray-800 fw-bold fs-7 d-block text-truncate" style="max-width: 200px;">{{ $far->failure_title }}</span>
                                    <span class="text-muted fs-8">Inv: {{ $far->investigator?->full_name ?: '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $far->failure_type_badge['class'] }} fs-8 fw-bold">{{ $far->failure_type_badge['label'] }}</span>
                                </td>
                                <td>
                                    <p class="text-gray-700 fs-8 text-truncate mb-0" style="max-width: 200px;">{{ $far->direct_cause ?: $far->root_cause_summary }}</p>
                                </td>
                                <td>
                                    <span class="text-danger fw-bold fs-8 d-block">{{ number_format($far->downtime_hours_estimate, 1) }} Jam D/T</span>
                                    <span class="text-muted fs-8">Rp {{ number_format($far->cost_impact_estimate, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $far->status_badge['class'] }} fs-8 fw-bold">{{ $far->status_badge['label'] }}</span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button wire:click="openDetailModal('{{ $far->id }}')" class="btn btn-icon btn-sm btn-light-info rounded-circle" title="Detail Laporan Investigasi">
                                            <i class="ki-outline ki-eye fs-5"></i>
                                        </button>
                                        <button wire:click="openEditModal('{{ $far->id }}')" class="btn btn-icon btn-sm btn-light-primary rounded-circle" title="Edit Laporan FAR">
                                            <i class="ki-outline ki-pencil fs-5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10 text-muted">
                                    <i class="ki-outline ki-shield-cross fs-3x text-muted mb-2"></i>
                                    <div class="fs-6 fw-bold">Belum Ada Laporan Investigasi FAR</div>
                                    <span class="fs-7">Buat laporan investigasi kegagalan teknis untuk mendokumentasikan akar masalah dan mencegah kejadian berulang.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-end mt-4">
                {{ $reports->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Form FAR (Create / Edit) -->
    @if($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header pb-0 border-0 justify-content-between">
                        <div>
                            <h3 class="fw-bold text-gray-900 mb-0">{{ $selectedFarId ? 'Edit Laporan Investigasi FAR' : 'Form Failure Analysis Report (FAR / TIR)' }}</h3>
                            <span class="fs-8 text-muted">Investigasi teknis kegagalan dini / kerusakan fatal komponen</span>
                        </div>
                        <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-2"></i>
                        </button>
                    </div>
                    <form wire:submit.prevent="saveFar">
                        <div class="modal-body py-4">
                            <!-- Section 1: Event & Asset Context -->
                            <div class="card bg-light-danger border-0 p-4 mb-4 rounded-3">
                                <h6 class="fw-bold text-danger mb-3"><i class="ki-outline ki-information-4 me-1"></i> Informasi Kejadian & Unit</h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label required fs-8 fw-bold">Tanggal Kerusakan</label>
                                        <input type="date" wire:model="incident_date" class="form-control form-control-sm bg-white rounded-2" required />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label required fs-8 fw-bold">Pilih Unit Equipment</label>
                                        <select wire:model.live="equipment_id" class="form-select form-select-sm bg-white rounded-2" required>
                                            <option value="">-- Pilih Unit --</option>
                                            @foreach($equipments as $eq)
                                                <option value="{{ $eq->id }}">{{ $eq->unit }} ({{ $eq->reffEquip->model ?? '' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fs-8 fw-bold">Komponen Terkait</label>
                                        <select wire:model.live="component_id" class="form-select form-select-sm bg-white rounded-2">
                                            <option value="">-- Bebas / Tanpa Master --</option>
                                            @foreach($components as $c)
                                                <option value="{{ $c->id }}">{{ $c->component_code }} - {{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label required fs-8 fw-bold">Klasifikasi Kerusakan</label>
                                        <select wire:model="failure_type" class="form-select form-select-sm bg-white rounded-2" required>
                                            <option value="premature_failure">Premature Failure (Aus Dini)</option>
                                            <option value="catastrophic_breakdown">Catastrophic Breakdown (Jebol)</option>
                                            <option value="fatigue_fracture">Fatigue Fracture (Patah Lelah)</option>
                                            <option value="lubrication_failure">Lubrication Starvation (Kurang Oli)</option>
                                            <option value="overheating">Thermal Overheating (Overheat)</option>
                                            <option value="operational_misuse">Operational Misuse (Salah Pakai)</option>
                                            <option value="assembly_error">Assembly / Installation Error</option>
                                            <option value="wear_out">Normal End of Life</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fs-8 fw-bold">HM Unit Saat Kejadian</label>
                                        <input type="number" step="0.1" wire:model="unit_hm_at_failure" class="form-control form-control-sm bg-white rounded-2" placeholder="Contoh: 14200.5" />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fs-8 fw-bold">HM Komponen Saat Kejadian</label>
                                        <input type="number" step="0.1" wire:model="component_hm_at_failure" class="form-control form-control-sm bg-white rounded-2" placeholder="Contoh: 2800.0" />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required fs-8 fw-bold">Judul Kasus Kerusakan</label>
                                        <input type="text" wire:model="failure_title" class="form-control form-control-sm bg-white rounded-2" placeholder="Contoh: Patah Crankshaft Engine Cyl #3 Akibat Starvasi Oli" required />
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Kronologi & Gejala -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label required fs-7 fw-bold">Kronologi Kejadian & Latar Belakang (Problem Statement)</label>
                                    <textarea wire:model="problem_statement" rows="3" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Uraikan apa yang terjadi, sedang beroperasi apa saat unit mendadak berhenti/rusak..." required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Gejala Fisik Awal Sebelum Kerusakan (Symptoms)</label>
                                    <textarea wire:model="failure_symptoms" rows="3" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Contoh: Asap hitam pekat, temperatur naik tiba-tiba, ada serbuk besi di filter oli..."></textarea>
                                </div>
                            </div>

                            <!-- Section 3: 5-Why Analysis Card -->
                            <div class="card border border-primary border-dashed p-4 mb-4 rounded-3 bg-light-primary">
                                <h6 class="fw-bold text-primary mb-3"><i class="ki-outline ki-filter-search me-1"></i> 5-Why Root Cause Analysis (Analisis 5 Mengapa)</h6>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text bg-primary text-white fw-bold w-90px">Why 1?</span>
                                            <input type="text" wire:model="why1" class="form-control bg-white" placeholder="Mengapa komponen mengalami kerusakan? (Gejala langsung)" />
                                        </div>
                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text bg-primary text-white fw-bold w-90px">Why 2?</span>
                                            <input type="text" wire:model="why2" class="form-control bg-white" placeholder="Mengapa Why 1 terjadi?" />
                                        </div>
                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text bg-primary text-white fw-bold w-90px">Why 3?</span>
                                            <input type="text" wire:model="why3" class="form-control bg-white" placeholder="Mengapa Why 2 terjadi?" />
                                        </div>
                                        <div class="input-group input-group-sm mb-2">
                                            <span class="input-group-text bg-primary text-white fw-bold w-90px">Why 4?</span>
                                            <input type="text" wire:model="why4" class="form-control bg-white" placeholder="Mengapa Why 3 terjadi?" />
                                        </div>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-danger text-white fw-bold w-90px">Why 5 (Root)</span>
                                            <input type="text" wire:model="why5" class="form-control bg-white fw-bold text-danger" placeholder="Akar masalah terdalam sistemik / prosedural..." />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 4: Cause & CAPA -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required fs-7 fw-bold">Penyebab Langsung (Direct Cause)</label>
                                    <textarea wire:model="direct_cause" rows="2" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Faktor teknis fisik langsung yang menyebabkan patahan/kerusakan..." required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Kesimpulan Akar Masalah (Root Cause Summary)</label>
                                    <textarea wire:model="root_cause_summary" rows="2" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Rangkuman akar masalah sistemik (misal: SOP pergantian filter diabaikan)..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Tindakan Korektif Cepat (Immediate Corrective Action)</label>
                                    <textarea wire:model="corrective_actions" rows="2" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Tindakan yang telah dilakukan segera untuk perbaikan unit..."></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Tindakan Pencegahan Masa Depan (Preventive Action / CAPA)</label>
                                    <textarea wire:model="preventive_actions" rows="2" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Revisi SOP, briefing mekanik, pengetatan inspeksi rutin agar tidak terulang..."></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-7 fw-bold">Estimasi Downtime (Jam)</label>
                                    <input type="number" step="0.5" wire:model="downtime_hours_estimate" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Contoh: 48.0" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-7 fw-bold">Estimasi Kerugian Biaya (Rp)</label>
                                    <input type="number" step="10000" wire:model="cost_impact_estimate" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Contoh: 150000000" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-7 fw-bold">Status Investigasi</label>
                                    <select wire:model="status" class="form-select form-select-sm form-select-solid rounded-2">
                                        <option value="under_investigation">Dalam Investigasi</option>
                                        <option value="review_manager">Review Plant Manager</option>
                                        <option value="closed">Closed & Selesai</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-sm btn-light">Batal</button>
                            <button type="submit" class="btn btn-sm btn-danger fw-bold">
                                <i class="ki-outline ki-check fs-4 me-1"></i> Simpan Laporan Investigasi FAR
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Detail FAR -->
    @if($showDetailModal && $activeFar)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header pb-0 border-0 justify-content-between">
                        <div>
                            <span class="badge {{ $activeFar->failure_type_badge['class'] }} mb-1">{{ $activeFar->failure_type_badge['label'] }}</span>
                            <h3 class="fw-bold text-gray-900 mb-0">FAR: {{ $activeFar->far_number }} - {{ $activeFar->failure_title }}</h3>
                            <span class="fs-8 text-muted">Tanggal Kejadian: {{ $activeFar->incident_date->format('d F Y') }} | Lead Investigator: {{ $activeFar->investigator?->full_name ?: '-' }}</span>
                        </div>
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-2"></i>
                        </button>
                    </div>
                    <div class="modal-body py-5">
                        <!-- Top Metadata Row -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded-3">
                                    <span class="fs-8 text-muted d-block fw-semibold">Unit Equipment</span>
                                    <span class="fs-6 fw-bold text-gray-900">{{ $activeFar->equipment->unit ?? 'N/A' }}</span>
                                    <span class="fs-8 text-muted d-block">HM Saat Rusak: {{ $activeFar->unit_hm_at_failure ? number_format($activeFar->unit_hm_at_failure, 1).' HM' : '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-light rounded-3">
                                    <span class="fs-8 text-muted d-block fw-semibold">Komponen</span>
                                    <span class="fs-6 fw-bold text-gray-900">{{ $activeFar->component ? $activeFar->component->name : 'N/A' }}</span>
                                    <span class="fs-8 text-muted d-block">HM Komponen: {{ $activeFar->component_hm_at_failure ? number_format($activeFar->component_hm_at_failure, 1).' HM' : '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-light-danger rounded-3">
                                    <span class="fs-8 text-danger d-block fw-semibold">Downtime Dampak</span>
                                    <span class="fs-5 fw-bolder text-danger">{{ number_format($activeFar->downtime_hours_estimate, 1) }} Jam</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 bg-light-danger rounded-3">
                                    <span class="fs-8 text-danger d-block fw-semibold">Estimasi Biaya Dampak</span>
                                    <span class="fs-6 fw-bolder text-danger">Rp {{ number_format($activeFar->cost_impact_estimate, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Problem Description -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-gray-900 mb-2">Kronologi Kerusakan (Problem Statement)</h6>
                            <div class="p-3 bg-light rounded-3 text-gray-800 fs-7">
                                {{ $activeFar->problem_statement }}
                            </div>
                        </div>

                        <!-- 5-Why Flow -->
                        @if($activeFar->root_cause_5why)
                            <div class="mb-4">
                                <h6 class="fw-bold text-primary mb-3">Silsilah Analisis 5-Why (Root Cause Investigation)</h6>
                                <div class="d-flex flex-column gap-2">
                                    @foreach($activeFar->root_cause_5why as $key => $whyText)
                                        @if(!empty($whyText))
                                            <div class="d-flex align-items-center p-2.5 {{ $key === 'why5' ? 'bg-light-danger border border-danger border-opacity-25' : 'bg-light' }} rounded-2 fs-7">
                                                <span class="badge {{ $key === 'why5' ? 'bg-danger' : 'bg-primary' }} text-white me-3 fw-bold">{{ strtoupper($key) }}</span>
                                                <span class="{{ $key === 'why5' ? 'fw-bold text-danger' : 'text-gray-800' }}">{{ $whyText }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Causes & CAPA Matrix -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="border border-gray-200 p-3 rounded-3 h-100">
                                    <h6 class="fw-bold text-gray-900 fs-7 mb-2 text-danger"><i class="ki-outline ki-cross-circle me-1"></i> Penyebab Langsung & Akar Masalah</h6>
                                    <p class="fs-7 text-gray-800 mb-2"><strong>Direct Cause:</strong> {{ $activeFar->direct_cause }}</p>
                                    @if($activeFar->root_cause_summary)
                                        <p class="fs-7 text-gray-700 mb-0"><strong>Root Cause:</strong> {{ $activeFar->root_cause_summary }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border border-success border-opacity-25 bg-light-success p-3 rounded-3 h-100">
                                    <h6 class="fw-bold text-success fs-7 mb-2"><i class="ki-outline ki-shield-tick me-1"></i> Tindakan Korektif & Preventif (CAPA)</h6>
                                    @if($activeFar->corrective_actions)
                                        <p class="fs-7 text-gray-800 mb-2"><strong>Immediate Action:</strong> {{ $activeFar->corrective_actions }}</p>
                                    @endif
                                    @if($activeFar->preventive_actions)
                                        <p class="fs-7 text-gray-800 mb-0"><strong>Preventive (CAPA):</strong> {{ $activeFar->preventive_actions }}</p>
                                    @endif
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
