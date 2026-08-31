<div>
    <!--begin::Page Header-->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-6 gap-3">
        <div>
            <h1 class="fs-2x fw-bolder text-gray-900 mb-1">
                <i class="ki-outline ki-chart-line-down fs-1 text-warning me-2"></i> PM Monitoring Service
            </h1>
            <span class="fs-7 text-muted fw-semibold">
                Monitoring jadwal Preventive Maintenance alat berat berdasarkan Hour Meter (HM) atau Kilometer (KM).
            </span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-light-primary fw-bold" wire:click="initializeSchedules()" onclick="return confirm('Inisialisasi jadwal PM untuk semua unit yang belum punya jadwal?')">
                <i class="ki-outline ki-abstract-10 fs-4 me-1"></i> Init Jadwal PM
            </button>
            <button type="button" class="btn btn-sm btn-light-warning fw-bold" wire:click="refreshSchedules()" onclick="return confirm('Refresh/rehitung semua jadwal PM?')">
                <i class="ki-outline ki-arrows-circle fs-4 me-1"></i> Refresh Semua
            </button>
        </div>
    </div>

    <!--begin::Success Message-->
    @if (session()->has('success'))
        <div class="alert alert-success d-flex align-items-center p-5 mb-6">
            <i class="ki-outline ki-check-circle fs-2hx text-success me-4"></i>
            <div class="d-flex flex-column">
                <h4 class="mb-1 text-success">Berhasil</h4>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!--begin::Metrics Row-->
    <div class="row g-4 mb-6">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">Total Jadwal</span>
                        <i class="ki-outline ki-calendar fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $totalSchedules }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background: linear-gradient(135deg, #f44336 0%, #b71c1c 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">Overdue</span>
                        <i class="ki-outline ki-warning fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $overdueCount }}</div>
                    <div class="text-white opacity-75 fs-9">Sudah melewati target</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background: linear-gradient(135deg, #ff8f00 0%, #e65100 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">Due Soon</span>
                        <i class="ki-outline ki-time fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $dueSoonCount }}</div>
                    <div class="text-white opacity-75 fs-9">≤ 7 hari lagi</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background: linear-gradient(135deg, #0288d1 0%, #01579b 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">Pending</span>
                        <i class="ki-outline ki-clock fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $pendingCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background: linear-gradient(135deg, #388e3c 0%, #1b5e20 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">Completed</span>
                        <i class="ki-outline ki-verify fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $completedCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <!--begin::Filter & Search-->
    <div class="card mb-6">
        <div class="card-header border-0 pt-5">
            <div class="card-toolbar">
                <div class="d-flex align-items-center position-relative my-1 me-3">
                    <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                    <input type="text" wire:model.live.debounce.500ms="search" class="form-control form-control-solid w-250px ps-13" placeholder="Cari unit atau service type..." />
                </div>
                <select wire:model.live="filterStatus" class="form-select form-select-solid w-150px me-2">
                    <option value="">Semua Status</option>
                    <option value="overdue">Overdue</option>
                    <option value="due_soon">Due Soon</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>
                <select wire:model.live="filterMeasurement" class="form-select form-select-solid w-150px">
                    <option value="">Semua Tipe</option>
                    <option value="hm">Hour Meter (HM)</option>
                    <option value="km">Kilometer (KM)</option>
                </select>
            </div>
        </div>
        <div class="card-body py-3">
            <div class="table-responsive">
                <table class="table table-row-dashed align-middle gs-0 gy-3 fs-7">
                    <thead>
                        <tr class="fw-bold text-muted fs-8 text-uppercase">
                            <th>Unit & Model</th>
                            <th>Service Type</th>
                            <th>Current HM/KM</th>
                            <th>Next Target</th>
                            <th>Progress</th>
                            <th>Next Plan Date</th>
                            <th>Remain Day</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                            @php
                                $equipment = $schedule->equipment;
                                $currentHm = (float) ($equipment->current_hm ?? 0);
                                $nextTarget = (float) ($schedule->next_target_hm_km ?? 0);
                                $lastExecuted = (float) ($schedule->last_executed_hm_km ?? 0);
                                $progress = $schedule->progress_percentage;
                            @endphp
                            <tr class="{{ $schedule->status === 'overdue' ? 'table-danger' : ($schedule->status === 'due_soon' ? 'table-warning' : '') }}">
                                <td>
                                    <span class="text-gray-900 fw-bold fs-7">{{ $equipment->unit ?? '-' }}</span>
                                    <span class="text-muted fs-9 d-block">{{ $equipment->reffEquip?->model ?? $equipment->pmUnitModel?->name ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-gray-800 fs-7">{{ $schedule->serviceType->name ?? '-' }}</span>
                                    <span class="badge {{ $schedule->serviceType->measurement_type_badge['class'] ?? '' }} fs-9 d-block w-fit mt-1">{{ strtoupper($schedule->serviceType->measurement_type ?? '') }}</span>
                                </td>
                                <td>
                                    <span class="text-gray-900 fw-bold fs-7">{{ number_format($currentHm, 0, ',', '.') }}</span>
                                    <span class="text-muted fs-9 d-block">Last: {{ number_format($lastExecuted, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    <span class="text-danger fw-bolder fs-7">{{ number_format($nextTarget, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress h-6px w-100px rounded-3">
                                            <div class="progress-bar {{ $progress >= 90 ? 'bg-danger' : ($progress >= 70 ? 'bg-warning' : 'bg-primary') }} rounded-3" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <span class="fw-bold fs-8 {{ $progress >= 90 ? 'text-danger' : ($progress >= 70 ? 'text-warning' : 'text-primary') }}">{{ number_format($progress, 0) }}%</span>
                                    </div>
                                </td>
                                <td>
                                    @if($schedule->next_plan_date)
                                        <span class="text-gray-900 fw-semibold fs-7">{{ $schedule->next_plan_date->format('d M Y') }}</span>
                                    @else
                                        <span class="text-muted fs-8">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($schedule->remain_days !== null)
                                        <span class="badge {{ $schedule->remain_days <= 0 ? 'badge-light-danger' : ($schedule->remain_days <= 7 ? 'badge-light-warning' : 'badge-light-primary') }} fs-8 fw-bold">
                                            {{ $schedule->remain_days }} hari
                                        </span>
                                    @else
                                        <span class="text-muted fs-8">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $schedule->status_badge['class'] }} fs-8 fw-bold">{{ $schedule->status_badge['label'] }}</span>
                                </td>
                                <td class="text-end">
                                    @if(in_array($schedule->status, ['overdue', 'due_soon', 'pending']))
                                        <button type="button" class="btn btn-sm btn-light-success me-1" wire:click="openGenerateModal('{{ $schedule->id }}')" title="Generate WO">
                                            <i class="ki-outline ki-abstract-26 fs-4"></i>
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-light-primary" wire:click="openDetailModal('{{ $schedule->id }}')" title="Detail">
                                        <i class="ki-outline ki-eye fs-4"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8">
                                    <i class="ki-outline ki-calendar fs-3x text-gray-300 mb-3 d-block"></i>
                                    <span class="text-muted fs-8">Belum ada data jadwal PM. Klik "Init Jadwal PM" untuk memulai.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <span class="text-muted fs-8">Menampilkan {{ $schedules->firstItem() ?? 0 }} - {{ $schedules->lastItem() ?? 0 }} dari {{ $schedules->total() }} data</span>
                {{ $schedules->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!--begin::Detail Modal-->
    @if($showDetailModal && $activeSchedule)
    <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" wire:click.self="$set('showDetailModal', false)">
        <div class="modal-dialog modal-dialog-centered mw-700px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Detail Jadwal PM: {{ $activeSchedule->equipment->unit ?? '-' }}</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" wire:click="$set('showDetailModal', false)">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body py-10 px-lg-17">
                    <div class="row g-5 mb-6">
                        <div class="col-md-6">
                            <div class="fs-8 text-muted mb-1">Unit</div>
                            <div class="fs-6 fw-bold text-gray-900">{{ $activeSchedule->equipment->unit ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="fs-8 text-muted mb-1">Model</div>
                            <div class="fs-6 fw-bold text-gray-900">{{ $activeSchedule->equipment->reffEquip?->model ?? $activeSchedule->equipment->pmUnitModel?->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="fs-8 text-muted mb-1">Service Type</div>
                            <div class="fs-6 fw-bold text-gray-900">{{ $activeSchedule->serviceType->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="fs-8 text-muted mb-1">Tipe Pengukuran</div>
                            <div class="fs-6 fw-bold text-gray-900">{{ strtoupper($activeSchedule->serviceType->measurement_type ?? '-') }} - Interval {{ number_format($activeSchedule->serviceType->interval_value ?? 0) }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="fs-8 text-muted mb-1">Current HM/KM</div>
                            <div class="fs-5 fw-bold text-primary">{{ number_format($activeSchedule->equipment->current_hm ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="fs-8 text-muted mb-1">Next Target</div>
                            <div class="fs-5 fw-bold text-danger">{{ number_format($activeSchedule->next_target_hm_km ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="fs-8 text-muted mb-1">Last Executed</div>
                            <div class="fs-5 fw-bold text-success">{{ number_format($activeSchedule->last_executed_hm_km ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="fs-8 text-muted mb-1">Next Plan Date</div>
                            <div class="fs-6 fw-bold text-gray-900">{{ $activeSchedule->next_plan_date ? $activeSchedule->next_plan_date->format('d M Y') : '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="fs-8 text-muted mb-1">Remain Days</div>
                            <div class="fs-6 fw-bold text-gray-900">{{ $activeSchedule->remain_days ?? '-' }} hari</div>
                        </div>
                    </div>

                    @if($activeSchedule->serviceType->tasks->count() > 0)
                        <h6 class="fw-bold text-gray-900 mb-3">Daftar Pekerjaan (Task)</h6>
                        @foreach($activeSchedule->serviceType->tasks as $task)
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <span class="badge badge-light-primary fs-8 fw-bold">#{{ $task->task_order }}</span>
                                <span class="fw-semibold text-gray-800 fs-7">{{ $task->task_title }}</span>
                                @if($task->parts->count() > 0)
                                    <span class="badge badge-light-dark fs-9">{{ $task->parts->count() }} Part</span>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!--begin::Generate WO Modal-->
    @if($showGenerateModal && $activeSchedule)
    <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" wire:click.self="$set('showGenerateModal', false)">
        <div class="modal-dialog modal-dialog-centered mw-500px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Generate Work Order</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" wire:click="$set('showGenerateModal', false)">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body py-10 px-lg-17">
                    <div class="alert alert-primary d-flex align-items-center p-4 mb-6">
                        <i class="ki-outline ki-information fs-2hx text-primary me-4"></i>
                        <div>
                            <span>Work Order akan dibuat dari jadwal PM <strong>{{ $activeSchedule->serviceType->name ?? '-' }}</strong> untuk unit <strong>{{ $activeSchedule->equipment->unit ?? '-' }}</strong>.</span>
                        </div>
                    </div>

                    <form wire:submit.prevent="confirmGenerate">
                        <div class="mb-8">
                            <label class="required fs-6 fw-semibold mb-2">HM/KM Saat Eksekusi</label>
                            <input type="number" wire:model="generate_hm_km" class="form-control form-control-solid" step="0.01" min="0" required />
                            <div class="form-text">Nilai HM/KM unit saat ini atau saat eksekusi dilakukan.</div>
                            @error('generate_hm_km') <span class="text-danger fs-7">{{ $message }}</span> @enderror
                        </div>

                        <div class="modal-footer flex-center">
                            <button type="button" wire:click="$set('showGenerateModal', false)" class="btn btn-light me-3">Batal</button>
                            <button type="submit" class="btn btn-success fw-bold" wire:loading.attr="disabled">
                                <i class="ki-outline ki-abstract-26 fs-4 me-1"></i>
                                <span wire:loading.remove wire:target="confirmGenerate">Generate WO</span>
                                <span wire:loading wire:target="confirmGenerate" class="indicator-progress">Memproses... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
