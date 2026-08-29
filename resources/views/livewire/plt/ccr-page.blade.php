<div class="d-flex flex-column flex-column-fluid">
    <!-- Toolbar -->
    <div class="d-flex flex-stack mb-6">
        <div>
            <h1 class="text-gray-900 fw-bold fs-2 mb-1">CCR - Component Condition Report</h1>
            <span class="text-muted fs-7">Laporan evaluasi kondisi fisik komponen, persentase keausan (Wear %), dan rekomendasi perbaikan/pergantian.</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button wire:click="openCreateModal" class="btn btn-primary btn-sm fw-bold">
                <i class="ki-outline ki-plus-circle fs-4 me-1"></i> Buat Laporan CCR
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
                        <i class="ki-outline ki-clipboard fs-2"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-gray-900">{{ $totalCcr }}</div>
                        <span class="fs-8 text-muted fw-semibold">Total Laporan CCR</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle bg-light-danger text-danger me-3">
                        <i class="ki-outline ki-shield-cross fs-2"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-gray-900">{{ $urgentReplaceCount }}</div>
                        <span class="fs-8 text-muted fw-semibold">Segera Ganti (Urgent Replace)</span>
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
                        <div class="fs-4 fw-bolder text-gray-900">{{ $scheduleChangeCount }}</div>
                        <span class="fs-8 text-muted fw-semibold">Jadwalkan Ganti (Planned)</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card card-flush shadow-xs border border-gray-200 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle bg-light-info text-info me-3">
                        <i class="ki-outline ki-wrench fs-2"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bolder text-gray-900">{{ $rebuildCount }}</div>
                        <span class="fs-8 text-muted fw-semibold">Rekomendasi Overhaul / Rebuild</span>
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
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control form-control-sm form-control-solid ps-9 w-225px rounded-pill fs-7" placeholder="Cari No. CCR, unit, komponen..." />
                </div>
                <select wire:model.live="recommendationFilter" class="form-select form-select-sm form-select-solid w-175px rounded-pill fs-7">
                    <option value="">Semua Rekomendasi</option>
                    <option value="continue_run">Lanjut Beroperasi (Good)</option>
                    <option value="monitor_next_service">Pantau Servis Berikutnya</option>
                    <option value="schedule_changeout">Jadwalkan Ganti (Planned)</option>
                    <option value="immediate_replace">Segera Ganti (Urgent)</option>
                    <option value="rebuild_overhaul">Rekondisi / Overhaul</option>
                    <option value="scrap">Afkir (Scrap)</option>
                </select>
                <select wire:model.live="statusFilter" class="form-select form-select-sm form-select-solid w-150px rounded-pill fs-7">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="reviewed_planner">Ditinjau Planner</option>
                    <option value="approved">Approved</option>
                </select>
            </div>
        </div>

        <!-- Card Body Table -->
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-3 fs-7">
                    <thead>
                        <tr class="fw-bolder text-muted bg-light">
                            <th class="ps-3 rounded-start">No. CCR & Tanggal</th>
                            <th>Unit Equipment</th>
                            <th>Komponen Diinspeksi</th>
                            <th>Keausan (Wear %)</th>
                            <th>Kondisi Fisik & Temuan</th>
                            <th>Rekomendasi Tindakan</th>
                            <th>Status & Follow-up</th>
                            <th class="text-end pe-3 rounded-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $ccr)
                            <tr>
                                <td class="ps-3">
                                    <a href="javascript:void(0)" wire:click="openDetailModal('{{ $ccr->id }}')" class="text-primary fw-bold fs-7 d-block">
                                        {{ $ccr->ccr_number }}
                                    </a>
                                    <span class="text-muted fs-8">{{ $ccr->ccr_date->format('d/m/Y') }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-30px symbol-circle bg-light-primary text-primary me-2">
                                            <i class="ki-outline ki-truck fs-5"></i>
                                        </div>
                                        <div>
                                            <span class="text-gray-900 fw-bold fs-7">{{ $ccr->equipment->unit ?? 'Unit N/A' }}</span>
                                            <span class="text-muted fs-8 d-block">{{ $ccr->current_unit_hm ? number_format($ccr->current_unit_hm, 1).' HM' : '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-gray-900 fw-bold">{{ $ccr->component_name }}</span>
                                    @if($ccr->component)
                                        <span class="text-muted fs-8 d-block">SN: {{ $ccr->component->serial_number ?: '-' }} ({{ number_format($ccr->component_running_hours ?: 0, 0) }} Jam)</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold fs-7 {{ $ccr->wear_percentage >= 85 ? 'text-danger' : ($ccr->wear_percentage >= 65 ? 'text-warning' : 'text-success') }}">
                                            {{ $ccr->wear_percentage }}%
                                        </span>
                                        <div class="progress h-6px w-60px bg-light-secondary rounded-pill">
                                            <div class="progress-bar rounded-pill {{ $ccr->wear_percentage >= 85 ? 'bg-danger' : ($ccr->wear_percentage >= 65 ? 'bg-warning' : 'bg-success') }}" style="width: {{ min(100, $ccr->wear_percentage) }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light-secondary text-capitalize fs-8 fw-semibold mb-1">{{ str_replace('_', ' ', $ccr->physical_condition) }}</span>
                                    <p class="text-gray-700 fs-8 text-truncate mb-0" style="max-width: 180px;">{{ $ccr->findings_description }}</p>
                                </td>
                                <td>
                                    <span class="badge {{ $ccr->recommendation_badge['class'] }} fs-8 fw-bold">{{ $ccr->recommendation_badge['label'] }}</span>
                                    @if($ccr->estimated_remaining_hours)
                                        <span class="text-muted fs-8 d-block mt-0.5">Sisa: ±{{ number_format($ccr->estimated_remaining_hours, 0) }} Jam</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ccr->work_order_id)
                                        <span class="badge badge-light-success fs-8 fw-bold">
                                            <i class="ki-outline ki-check-circle text-success fs-7 me-1"></i> WO Created
                                        </span>
                                    @else
                                        <span class="badge {{ $ccr->status_badge['class'] }} fs-8 fw-bold">{{ $ccr->status_badge['label'] }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-flex justify-content-end gap-1">
                                        @if(!$ccr->work_order_id && in_array($ccr->recommendation, ['schedule_changeout', 'immediate_replace', 'rebuild_overhaul']))
                                            <button wire:click="createWorkOrderFromCcr('{{ $ccr->id }}')" class="btn btn-icon btn-sm btn-light-warning rounded-circle" title="Buat Work Order Tindak Lanjut" onclick="return confirm('Buat Work Order otomatis dari laporan CCR ini?')">
                                                <i class="ki-outline ki-wrench fs-5"></i>
                                            </button>
                                        @endif
                                        <button wire:click="openDetailModal('{{ $ccr->id }}')" class="btn btn-icon btn-sm btn-light-info rounded-circle" title="Detail Evaluasi CCR">
                                            <i class="ki-outline ki-eye fs-5"></i>
                                        </button>
                                        <button wire:click="openEditModal('{{ $ccr->id }}')" class="btn btn-icon btn-sm btn-light-primary rounded-circle" title="Edit Laporan CCR">
                                            <i class="ki-outline ki-pencil fs-5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10 text-muted">
                                    <i class="ki-outline ki-clipboard fs-3x text-muted mb-2"></i>
                                    <div class="fs-6 fw-bold">Belum Ada Laporan CCR</div>
                                    <span class="fs-7">Buat laporan evaluasi kondisi komponen pertama untuk memantau sisa umur dan keausan.</span>
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

    <!-- Modal Form (Create / Edit CCR) -->
    @if($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header pb-0 border-0 justify-content-between">
                        <h3 class="fw-bold text-gray-900">{{ $selectedCcrId ? 'Edit Laporan CCR' : 'Buat Form Component Condition Report (CCR)' }}</h3>
                        <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-2"></i>
                        </button>
                    </div>
                    <form wire:submit.prevent="saveCcr">
                        <div class="modal-body py-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required fs-7 fw-bold">Tanggal Inspeksi</label>
                                    <input type="date" wire:model="ccr_date" class="form-control form-control-sm form-control-solid rounded-2" required />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fs-7 fw-bold">Pilih Unit Equipment</label>
                                    <select wire:model.live="equipment_id" class="form-select form-select-sm form-select-solid rounded-2" required>
                                        <option value="">-- Pilih Unit --</option>
                                        @foreach($equipments as $eq)
                                            <option value="{{ $eq->id }}">{{ $eq->unit }} ({{ $eq->reffEquip->model ?? '' }})</option>
                                        @endforeach
                                    </select>
                                    @error('equipment_id') <span class="text-danger fs-8">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Pilih Dari Master Komponen (Opsional)</label>
                                    <select wire:model.live="component_id" class="form-select form-select-sm form-select-solid rounded-2">
                                        <option value="">-- Komponen Manual / Belum Terdaftar --</option>
                                        @foreach($components as $c)
                                            <option value="{{ $c->id }}">{{ $c->component_code }} - {{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fs-7 fw-bold">Nama Komponen Yang Diinspeksi</label>
                                    <input type="text" wire:model="component_name" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Contoh: Hydraulic Pump Main, Final Drive LH" required />
                                    @error('component_name') <span class="text-danger fs-8">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Hourmeter Unit Saat Inspeksi (HM)</label>
                                    <input type="number" step="0.1" wire:model="current_unit_hm" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Contoh: 8520.4" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required fs-7 fw-bold">Tingkat Keausan (Wear Percentage %)</label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="1" min="0" max="100" wire:model="wear_percentage" class="form-control form-control-solid rounded-start" placeholder="Contoh: 75" required />
                                        <span class="input-group-text bg-light">%</span>
                                    </div>
                                    @error('wear_percentage') <span class="text-danger fs-8">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="col-12"><hr class="my-1 text-muted opacity-25"></div>

                                <!-- Condition Checklist -->
                                <div class="col-md-3">
                                    <label class="form-label fs-7 fw-bold">Kondisi Fisik</label>
                                    <select wire:model="physical_condition" class="form-select form-select-sm form-select-solid rounded-2">
                                        <option value="good">Good (Normal)</option>
                                        <option value="fair_wear">Fair Wear (Aus Wajar)</option>
                                        <option value="critical_wear">Critical Wear (Aus Kritis)</option>
                                        <option value="damaged">Damaged (Retak/Patah)</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fs-7 fw-bold">Status Kebocoran</label>
                                    <select wire:model="leakage_status" class="form-select form-select-sm form-select-solid rounded-2">
                                        <option value="none">Tidak Ada (Kering)</option>
                                        <option value="minor_sweating">Rembes Halus (Sweating)</option>
                                        <option value="dripping">Menetes (Dripping)</option>
                                        <option value="severe_stream">Bocor Deras (Severe)</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fs-7 fw-bold">Getaran & Suara</label>
                                    <select wire:model="noise_vibration_status" class="form-select form-select-sm form-select-solid rounded-2">
                                        <option value="normal">Normal Halus</option>
                                        <option value="abnormal_noise">Suara Kasar / Gemeretak</option>
                                        <option value="high_vibration">Getaran Tinggi (Vibrasi)</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fs-7 fw-bold">Kondisi Oli / Cairan</label>
                                    <select wire:model="oil_contamination_status" class="form-select form-select-sm form-select-solid rounded-2">
                                        <option value="clean">Bersih & Bening</option>
                                        <option value="slight_metal">Ada Serbuk Metal Halus</option>
                                        <option value="burned_dark">Hitam Gosong / Terbakar</option>
                                        <option value="water_emulsified">Terkontaminasi Air (Susu)</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label required fs-7 fw-bold">Deskripsi Temuan Hasil Inspeksi Lapangan</label>
                                    <textarea wire:model="findings_description" rows="3" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Uraikan kondisi visual, hasil pengukuran backlash, kedalaman aus..." required></textarea>
                                    @error('findings_description') <span class="text-danger fs-8">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required fs-7 fw-bold">Rekomendasi Tindakan</label>
                                    <select wire:model="recommendation" class="form-select form-select-sm form-select-solid rounded-2" required>
                                        <option value="continue_run">Lanjut Beroperasi (Good - Safe to Run)</option>
                                        <option value="monitor_next_service">Pantau Berkala di Servis Berikutnya</option>
                                        <option value="schedule_changeout">Jadwalkan Ganti Terencana (Planned Changeout)</option>
                                        <option value="immediate_replace">Segera Ganti (Urgent Immediate Replace)</option>
                                        <option value="rebuild_overhaul">Rekondisi / Overhaul Bengkel</option>
                                        <option value="scrap">Afkir / Rusak Total (Scrap)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Estimasi Sisa Umur (Jam Operasi)</label>
                                    <input type="number" step="10" wire:model="estimated_remaining_hours" class="form-control form-control-sm form-control-solid rounded-2" placeholder="Contoh: 500 Jam" />
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-sm btn-light">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary fw-bold">
                                <i class="ki-outline ki-check fs-4 me-1"></i> Simpan Laporan CCR
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Detail CCR -->
    @if($showDetailModal && $activeCcr)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header pb-0 border-0 justify-content-between">
                        <div>
                            <span class="badge {{ $activeCcr->recommendation_badge['class'] }} mb-1">{{ $activeCcr->recommendation_badge['label'] }}</span>
                            <h3 class="fw-bold text-gray-900 mb-0">Laporan CCR: {{ $activeCcr->ccr_number }}</h3>
                            <span class="fs-8 text-muted">Tanggal: {{ $activeCcr->ccr_date->format('d F Y') }} | Inspektor: {{ $activeCcr->inspector?->full_name ?: '-' }}</span>
                        </div>
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-2"></i>
                        </button>
                    </div>
                    <div class="modal-body py-5">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="text-muted fs-8 fw-semibold">Unit Equipment</div>
                                    <div class="fs-6 fw-bold text-gray-900">{{ $activeCcr->equipment->unit ?? 'N/A' }} ({{ $activeCcr->equipment->reffEquip->model ?? '' }})</div>
                                    <span class="fs-8 text-muted">HM Saat Inspeksi: {{ $activeCcr->current_unit_hm ? number_format($activeCcr->current_unit_hm, 1).' HM' : '-' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="text-muted fs-8 fw-semibold">Komponen Diinspeksi</div>
                                    <div class="fs-6 fw-bold text-gray-900">{{ $activeCcr->component_name }}</div>
                                    <span class="fs-8 text-muted">{{ $activeCcr->component ? 'Kode: '.$activeCcr->component->component_code.' | S/N: '.$activeCcr->component->serial_number : 'Komponen Terpasang' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Parameters Matrix -->
                        <h6 class="fw-bold text-gray-900 mb-3">Hasil Evaluasi Parameter Kondisi</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-md-3">
                                <div class="border border-gray-200 p-3 rounded-2 text-center">
                                    <span class="fs-8 text-muted d-block">Tingkat Keausan</span>
                                    <span class="fs-4 fw-bolder {{ $activeCcr->wear_percentage >= 80 ? 'text-danger' : 'text-primary' }}">{{ $activeCcr->wear_percentage }}%</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border border-gray-200 p-3 rounded-2 text-center">
                                    <span class="fs-8 text-muted d-block">Fisik Visual</span>
                                    <span class="fs-7 fw-bold text-capitalize text-gray-800">{{ str_replace('_', ' ', $activeCcr->physical_condition) }}</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border border-gray-200 p-3 rounded-2 text-center">
                                    <span class="fs-8 text-muted d-block">Kebocoran</span>
                                    <span class="fs-7 fw-bold text-capitalize text-gray-800">{{ str_replace('_', ' ', $activeCcr->leakage_status) }}</span>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="border border-gray-200 p-3 rounded-2 text-center">
                                    <span class="fs-8 text-muted d-block">Getaran/Suara</span>
                                    <span class="fs-7 fw-bold text-capitalize text-gray-800">{{ str_replace('_', ' ', $activeCcr->noise_vibration_status) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold text-gray-900 mb-2">Deskripsi Temuan Lapangan</h6>
                            <div class="p-4 bg-light rounded-3 text-gray-800 fs-7">
                                {{ $activeCcr->findings_description }}
                            </div>
                        </div>

                        <div class="p-4 bg-light-warning rounded-3 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fs-8 text-muted d-block fw-bold">Rekomendasi Plant Planner:</span>
                                <span class="fs-6 fw-bolder text-gray-900">{{ $activeCcr->recommendation_badge['label'] }}</span>
                                @if($activeCcr->estimated_remaining_hours)
                                    <span class="fs-8 text-muted d-block">Estimasi Sisa Umur Aman: ±{{ number_format($activeCcr->estimated_remaining_hours, 0) }} Jam Operasi</span>
                                @endif
                            </div>
                            @if($activeCcr->work_order_id)
                                <div class="badge badge-success p-2 fs-7 fw-bold">
                                    Follow-up: WO {{ $activeCcr->workOrder?->wo_number }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        @if(!$activeCcr->work_order_id && in_array($activeCcr->recommendation, ['schedule_changeout', 'immediate_replace', 'rebuild_overhaul']))
                            <button type="button" wire:click="createWorkOrderFromCcr('{{ $activeCcr->id }}')" class="btn btn-sm btn-warning fw-bold">
                                <i class="ki-outline ki-wrench fs-4 me-1"></i> Buat Work Order Langsung
                            </button>
                        @endif
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-sm btn-light">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
