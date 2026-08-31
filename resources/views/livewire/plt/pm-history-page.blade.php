<div>
    <!--begin::Page Header-->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-6 gap-3">
        <div>
            <h1 class="fs-2x fw-bolder text-gray-900 mb-1">
                <i class="ki-outline ki-clock fs-1 text-info me-2"></i> PM History Service
            </h1>
            <span class="fs-7 text-muted fw-semibold">
                Riwayat Work Order Preventive Maintenance yang sudah dijalankan.
            </span>
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
        <div class="col-xl-3 col-md-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">Total History</span>
                        <i class="ki-outline ki-archive fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $totalHistory }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
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
        <div class="col-xl-3 col-md-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background: linear-gradient(135deg, #ff8f00 0%, #e65100 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">In Progress</span>
                        <i class="ki-outline ki-time fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $inProgressCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background: linear-gradient(135deg, #0288d1 0%, #01579b 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">Generated</span>
                        <i class="ki-outline ki-abstract-26 fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $generatedCount }}</div>
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
                    <input type="text" wire:model.live.debounce.500ms="search" class="form-control form-control-solid w-250px ps-13" placeholder="Cari nomor WO, unit, atau service type..." />
                </div>
                <select wire:model.live="filterStatus" class="form-select form-select-solid w-150px">
                    <option value="">Semua Status</option>
                    <option value="generated">Generated</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
        </div>
        <div class="card-body py-3">
            <div class="table-responsive">
                <table class="table table-row-dashed align-middle gs-0 gy-3 fs-7">
                    <thead>
                        <tr class="fw-bold text-muted fs-8 text-uppercase">
                            <th>No. WO</th>
                            <th>Unit</th>
                            <th>Service Type</th>
                            <th>HM/KM Eksekusi</th>
                            <th>Tanggal</th>
                            <th>Status WO</th>
                            <th>Status PM</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pmWorkOrders as $pmWo)
                            @php
                                $wo = $pmWo->workOrder;
                                $schedule = $pmWo->schedule;
                            @endphp
                            <tr>
                                <td>
                                    <span class="text-gray-900 fw-bold fs-7">{{ $wo->wo_number ?? '-' }}</span>
                                    <span class="text-muted fs-9 d-block">{{ $wo->wo_date?->format('d M Y') ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="text-gray-900 fw-bold fs-7">{{ $schedule->equipment->unit ?? $wo->equipment->unit ?? '-' }}</span>
                                    <span class="text-muted fs-9 d-block">{{ $wo->equipment->reffEquip?->model ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-gray-800 fs-7">{{ $schedule->serviceType->name ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary fs-7">{{ number_format($pmWo->hm_km_at_execution ?? 0, 0, ',', '.') }}</span>
                                    <span class="badge badge-light-primary fs-9 ms-1">{{ strtoupper($schedule->serviceType->measurement_type ?? '') }}</span>
                                </td>
                                <td>
                                    <span class="text-gray-900 fw-semibold fs-7">{{ $pmWo->execution_date?->format('d M Y') ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $wo->status_badge['class'] ?? 'badge-light-secondary' }} fs-8 fw-bold">{{ $wo->status_badge['label'] ?? ucfirst($wo->status ?? '-') }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $pmWo->status_badge['class'] }} fs-8 fw-bold">{{ $pmWo->status_badge['label'] }}</span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-light-primary" wire:click="openDetailModal('{{ $pmWo->id }}')" title="Detail">
                                        <i class="ki-outline ki-eye fs-4"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-8">
                                    <i class="ki-outline ki-archive fs-3x text-gray-300 mb-3 d-block"></i>
                                    <span class="text-muted fs-8">Belum ada riwayat PM Service.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <span class="text-muted fs-8">Menampilkan {{ $pmWorkOrders->firstItem() ?? 0 }} - {{ $pmWorkOrders->lastItem() ?? 0 }} dari {{ $pmWorkOrders->total() }} data</span>
                {{ $pmWorkOrders->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!--begin::Detail Modal-->
    @if($showDetailModal && $activePmWo)
    <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" wire:click.self="$set('showDetailModal', false)">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Detail PM History: {{ $activePmWo->workOrder->wo_number ?? '-' }}</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" wire:click="$set('showDetailModal', false)">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body py-10 px-lg-17">
                    <div class="row g-5 mb-6">
                        <div class="col-md-6">
                            <div class="fs-8 text-muted mb-1">No. WO</div>
                            <div class="fs-6 fw-bold text-gray-900">{{ $activePmWo->workOrder->wo_number ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="fs-8 text-muted mb-1">Unit</div>
                            <div class="fs-6 fw-bold text-gray-900">{{ $activePmWo->schedule->equipment->unit ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="fs-8 text-muted mb-1">Service Type</div>
                            <div class="fs-6 fw-bold text-gray-900">{{ $activePmWo->schedule->serviceType->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="fs-8 text-muted mb-1">HM/KM Eksekusi</div>
                            <div class="fs-6 fw-bold text-primary">{{ number_format($activePmWo->hm_km_at_execution ?? 0, 0, ',', '.') }} {{ strtoupper($activePmWo->schedule->serviceType->measurement_type ?? '') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="fs-8 text-muted mb-1">Tanggal Eksekusi</div>
                            <div class="fs-6 fw-bold text-gray-900">{{ $activePmWo->execution_date?->format('d M Y') ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="fs-8 text-muted mb-1">Requester</div>
                            <div class="fs-6 fw-bold text-gray-900">{{ $activePmWo->workOrder->requester->full_name ?? '-' }}</div>
                        </div>
                    </div>

                    @if($activePmWo->workOrder->tasks->count() > 0)
                        <h6 class="fw-bold text-gray-900 mb-3">Daftar Pekerjaan (Task)</h6>
                        @foreach($activePmWo->workOrder->tasks as $task)
                            <div class="card card-flush shadow-sm border border-gray-200 mb-3">
                                <div class="card-header pt-3 pb-2">
                                    <div class="card-title">
                                        <span class="badge badge-light-primary fs-8 fw-bold me-2">Task #{{ $task->task_order }}</span>
                                        <span class="fw-bold text-gray-900 fs-7">{{ $task->problem_title }}</span>
                                    </div>
                                    <div class="card-toolbar">
                                        <span class="badge {{ $task->status_badge['class'] }} fs-8">{{ $task->status_badge['label'] }}</span>
                                    </div>
                                </div>
                                @if($task->subtasks->count() > 0)
                                    <div class="card-body pt-0">
                                        @foreach($task->subtasks as $subtask)
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge badge-light-secondary fs-9">#{{ $subtask->subtask_order }}</span>
                                                <span class="fs-8 text-gray-800">{{ $subtask->action_title }}</span>
                                                <span class="badge {{ $subtask->status_badge['class'] }} fs-9">{{ $subtask->status_badge['label'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
