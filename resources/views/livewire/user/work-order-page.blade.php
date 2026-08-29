<div>
    <!--begin::Row (KPI Stat Cards)-->
    <div class="row g-5 g-xl-8 mb-6">
        <!-- Total WO -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-flush shadow-sm border border-gray-200 h-100 bg-white">
                <div class="card-body d-flex align-items-center py-5">
                    <div class="symbol symbol-50px me-4">
                        <div class="symbol-label bg-light-primary text-primary">
                            <i class="ki-outline ki-document fs-2x text-primary"></i>
                        </div>
                    </div>
                    <div>
                        <div class="fs-2hx fw-bolder text-gray-900 leading-tight">{{ $metrics['total'] }}</div>
                        <div class="fs-7 fw-semibold text-muted">Total Work Orders</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-flush shadow-sm border border-gray-200 h-100 bg-white">
                <div class="card-body d-flex align-items-center py-5">
                    <div class="symbol symbol-50px me-4">
                        <div class="symbol-label bg-light-warning text-warning">
                            <i class="ki-outline ki-time fs-2x text-warning"></i>
                        </div>
                    </div>
                    <div>
                        <div class="fs-2hx fw-bolder text-warning leading-tight">{{ $metrics['in_progress'] }}</div>
                        <div class="fs-7 fw-semibold text-muted">Sedang Dikerjakan</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Breakdown Unit -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-flush shadow-sm border border-gray-200 h-100 bg-white">
                <div class="card-body d-flex align-items-center py-5">
                    <div class="symbol symbol-50px me-4">
                        <div class="symbol-label bg-light-danger text-danger">
                            <i class="ki-outline ki-abstract-26 fs-2x text-danger"></i>
                        </div>
                    </div>
                    <div>
                        <div class="fs-2hx fw-bolder text-danger leading-tight">{{ $metrics['breakdown'] }}</div>
                        <div class="fs-7 fw-semibold text-muted">Unit Breakdown (BD)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed -->
        <div class="col-xl-3 col-md-6">
            <div class="card card-flush shadow-sm border border-gray-200 h-100 bg-white">
                <div class="card-body d-flex align-items-center py-5">
                    <div class="symbol symbol-50px me-4">
                        <div class="symbol-label bg-light-success text-success">
                            <i class="ki-outline ki-check-circle fs-2x text-success"></i>
                        </div>
                    </div>
                    <div>
                        <div class="fs-2hx fw-bolder text-success leading-tight">{{ $metrics['completed'] }}</div>
                        <div class="fs-7 fw-semibold text-muted">Selesai (Completed)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Row-->

    <!--begin::Main Card-->
    <div class="card card-flush shadow-sm border border-gray-200">
        <!--begin::Card header-->
        <div class="card-header pt-7 pb-5">
            <div class="d-flex flex-wrap align-items-center justify-content-between w-100 gap-4">
                <!-- Search & Filters -->
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="position-relative">
                        <i class="ki-outline ki-magnifier fs-3 position-absolute top-50 translate-middle-y ms-4 text-gray-500"></i>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            class="form-control form-control-solid form-control-sm w-200px w-lg-250px ps-11 fs-7"
                            placeholder="Cari No. WO, Unit, Problem..."
                        />
                    </div>

                    <!-- Status Filter -->
                    <select wire:model.live="filterStatus" class="form-select form-select-solid form-select-sm w-150px fs-7">
                        <option value="all">Semua Status</option>
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="waiting_part">Waiting Part</option>
                        <option value="completed">Completed</option>
                        <option value="closed">Closed</option>
                    </select>

                    <!-- Type Filter -->
                    <select wire:model.live="filterType" class="form-select form-select-solid form-select-sm w-150px fs-7">
                        <option value="all">Semua Tipe</option>
                        <option value="plan">Plan (PM Plan)</option>
                        <option value="preventive">Preventive (PM)</option>
                        <option value="corrective">Corrective (CM)</option>
                        <option value="breakdown">Breakdown (BD)</option>
                        <option value="inspection">Inspection</option>
                        <option value="overhaul">Overhaul</option>
                    </select>
                </div>

                <!-- Create WO Button -->
                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="btn btn-sm btn-primary fw-bold"
                >
                    <i class="ki-outline ki-plus fs-3 me-1"></i> Buat Work Order Baru
                </button>
            </div>
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-bordered table-row-dashed align-middle gy-3 gs-3 fs-7">
                    <thead class="bg-light text-muted fw-bold fs-8 text-uppercase gs-0">
                        <tr>
                            <th>No. Work Order</th>
                            <th>Unit & Status Unit</th>
                            <th>Problem & Action</th>
                            <th>Tipe & Prioritas</th>
                            <th>Waktu BD & Ready</th>
                            <th>Status WO</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-700">
                        @forelse($workOrders as $wo)
                            <tr>
                                <!-- WO Number & Date -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <a
                                            href="javascript:void(0)"
                                            wire:click="openDetailModal('{{ $wo->id }}')"
                                            class="text-gray-900 text-hover-primary fw-bold fs-6"
                                        >
                                            {{ $wo->wo_number }}
                                        </a>
                                        <span class="text-muted fs-8">
                                            {{ $wo->wo_date->format('d M Y') }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Equipment & Unit Status -->
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-35px symbol-circle me-3 bg-light-primary text-primary">
                                            <i class="ki-outline ki-setting-3 fs-3 text-primary"></i>
                                        </div>
                                        <div class="d-flex flex-column gap-1">
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="text-gray-900 fw-bold fs-7">{{ $wo->equipment->unit ?? 'Unit N/A' }}</span>
                                                <span class="badge {{ $wo->unit_status_badge['class'] }} fs-9 py-0 px-1">
                                                    {{ $wo->unit_status_badge['label'] }}
                                                </span>
                                            </div>
                                            <span class="text-muted fs-8">
                                                {{ $wo->equipment->reffEquip->model ?? '' }} ({{ $wo->site->site_name ?? 'Site N/A' }})
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Problems & Actions & Obstacles -->
                                <td>
                                    <div class="d-flex flex-column gap-1" style="max-width: 270px;">
                                        @if($wo->tasks->count() > 0)
                                            @foreach($wo->tasks as $t)
                                                <div class="d-flex align-items-center gap-1">
                                                    @if($t->is_primary)
                                                        <span class="badge badge-sm badge-light-danger fw-bold fs-9 py-0 px-1">Primary</span>
                                                    @endif
                                                    <span class="text-gray-900 fw-bold fs-8 text-truncate" title="{{ $t->problem_title }}">
                                                        {{ $t->problem_title }}
                                                    </span>
                                                </div>
                                                <div class="ps-2 border-start border-2 border-gray-300 mb-1">
                                                    @foreach($t->subtasks as $st)
                                                        <div class="d-flex flex-wrap align-items-center gap-1 text-muted fs-9">
                                                            <span class="text-truncate" style="max-width: 170px;" title="{{ $st->action_title }}">
                                                                • {{ $st->action_title }}
                                                            </span>
                                                            @if($st->obstacle && $st->obstacle !== 'none')
                                                                <span class="badge {{ $st->obstacle_badge['class'] }} fs-9 py-0 px-1">
                                                                    {{ $st->obstacle_badge['label'] }}
                                                                </span>
                                                            @endif
                                                            @if($st->spareparts->count() > 0)
                                                                <span class="text-primary fw-semibold">({{ $st->spareparts->count() }} part)</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-gray-900 fw-bold text-truncate">{{ $wo->job_title }}</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Type, Opportunity & Priority -->
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge {{ $wo->type_badge['class'] }} fs-8 fw-bold w-fit px-2 py-0.5">
                                                {{ $wo->type_badge['label'] }}
                                            </span>
                                            @if($wo->is_opportunity)
                                                <span class="badge badge-light-warning fs-9 fw-bold px-1 py-0.5 text-warning" title="Pekerjaan Opportunity Maintenance">
                                                    <i class="ki-outline ki-flash fs-9 text-warning"></i> Opportunity
                                                </span>
                                            @endif
                                        </div>
                                        <span class="badge {{ $wo->priority_badge['class'] }} fs-8 fw-semibold w-fit px-2 py-0.5">
                                            {{ $wo->priority_badge['label'] }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Breakdown & Ready Timeline -->
                                <td>
                                    <div class="d-flex flex-column gap-1 fs-8">
                                        <div class="d-flex align-items-center text-gray-800">
                                            <i class="ki-outline ki-time fs-6 text-danger me-1" title="Waktu Breakdown"></i>
                                            <span>{{ $wo->breakdown_at ? $wo->breakdown_at->format('d/m/y H:i') : '-' }}</span>
                                        </div>
                                        <div class="d-flex align-items-center {{ $wo->ready_at ? 'text-success' : 'text-muted' }}">
                                            <i class="ki-outline ki-check-circle fs-6 {{ $wo->ready_at ? 'text-success' : 'text-gray-400' }} me-1" title="Waktu Ready"></i>
                                            <span>{{ $wo->ready_at ? $wo->ready_at->format('d/m/y H:i') : 'Belum Ready' }}</span>
                                        </div>
                                        @if($wo->downtime_hours > 0)
                                            <span class="badge badge-light-danger fs-9 py-0 px-1 w-fit">
                                                Downtime: {{ $wo->downtime_hours }} Jam
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Status -->
                                <td>
                                    <span class="badge {{ $wo->status_badge['class'] }} fs-7 fw-bold px-3 py-1">
                                        {{ $wo->status_badge['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-10">
                                    <div class="symbol symbol-65px symbol-circle bg-light-primary text-primary mx-auto mb-3">
                                        <i class="ki-outline ki-document-filter fs-2tx"></i>
                                    </div>
                                    <p class="fs-6 fw-semibold text-gray-600 mb-0">Belum ada data Work Order.</p>
                                    <span class="fs-8 text-muted">Klik tombol "Buat Work Order Baru" untuk menambahkan.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $workOrders->links() }}
            </div>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Main Card-->

    <!--begin::Modal Form Buat / Edit WO Hierarki-->
    @if($showFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.6); z-index: 1055;">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 96vw; width: 96vw; margin: 1.5rem auto;">
                <div class="modal-content rounded-4 border-0 shadow-lg" style="height: 92vh;">
                    <div class="modal-header border-bottom py-4 px-6 bg-light-primary">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px symbol-circle bg-primary text-white me-3 d-flex align-items-center justify-content-center">
                                <i class="ki-outline ki-wrench fs-3 text-white"></i>
                            </div>
                            <div>
                                <h4 class="modal-title fw-bolder text-gray-900 mb-0">
                                    {{ $editId ? 'Edit Work Order (Hierarki Problem & Action)' : 'Buat Work Order Baru (Hierarki Problem & Action)' }}
                                </h4>
                                <span class="text-muted fs-8">Form Manajemen Pemeliharaan Unit & Penugasan Terintegrasi</span>
                            </div>
                        </div>
                        <button
                            type="button"
                            wire:click="$set('showFormModal', false)"
                            class="btn btn-sm btn-icon btn-light"
                        >
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <form wire:submit="saveWorkOrder" class="d-flex flex-column flex-grow-1 overflow-hidden">
                        <div class="modal-body py-5 px-7 overflow-y-auto flex-grow-1">
                            <!-- Bagian 1: Header 3 Kategori Kartu (Unit, Klasifikasi, dan Timeline Waktu) -->
                            <div class="row g-4 mb-6">
                                <!-- Card 1: Unit & Lokasi Armada -->
                                <div class="col-lg-4">
                                    <div class="card h-100 border border-gray-300 shadow-xs rounded-3 bg-white">
                                        <div class="card-header py-3 px-4 min-h-auto bg-light-primary border-bottom d-flex align-items-center">
                                            <div class="symbol symbol-30px symbol-circle bg-primary text-white me-2.5 d-flex align-items-center justify-content-center">
                                                <i class="ki-outline ki-truck fs-5 text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="fs-7 fw-bolder text-gray-900 mb-0">1. Unit & Lokasi Site</h6>
                                                <span class="text-muted fs-9">Identitas armada & operasional</span>
                                            </div>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label required fs-8 fw-bold">Pilih Unit Equipment</label>
                                                <select wire:model.live="equipment_id" class="form-select form-select-sm form-select-solid fs-8">
                                                    <option value="">-- Pilih Unit Alat --</option>
                                                    @foreach($equipments as $eq)
                                                        <option value="{{ $eq->id }}">
                                                            {{ $eq->unit }} ({{ $eq->reffEquip->model ?? 'Model N/A' }}) - {{ $eq->reffEquip->make ?? '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('equipment_id') <span class="text-danger fs-9 mt-1 d-block">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fs-8 fw-bold">Lokasi Site / Tambang</label>
                                                <select wire:model="site_id" class="form-select form-select-sm form-select-solid fs-8">
                                                    <option value="">-- Pilih Lokasi Site --</option>
                                                    @foreach($sites as $st)
                                                        <option value="{{ $st->id }}">{{ $st->site_name }} ({{ $st->site_code }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <label class="form-label fs-8 fw-bold">Hour Meter (HM)</label>
                                                    <input type="number" step="0.1" wire:model="current_hm" class="form-control form-control-sm form-control-solid fs-8" placeholder="HM: 3520.5" />
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fs-8 fw-bold">Kilometer (KM)</label>
                                                    <input type="number" step="0.1" wire:model="current_km" class="form-control form-control-sm form-control-solid fs-8" placeholder="KM: 12500" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 2: Klasifikasi & Status Unit -->
                                <div class="col-lg-4">
                                    <div class="card h-100 border border-gray-300 shadow-xs rounded-3 bg-white">
                                        <div class="card-header py-3 px-4 min-h-auto bg-light-info border-bottom d-flex align-items-center">
                                            <div class="symbol symbol-30px symbol-circle bg-info text-white me-2.5 d-flex align-items-center justify-content-center">
                                                <i class="ki-outline ki-category fs-5 text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="fs-7 fw-bolder text-gray-900 mb-0">2. Klasifikasi & Status Unit</h6>
                                                <span class="text-muted fs-9">Tipe maintenance & status armada</span>
                                            </div>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row g-2 mb-3">
                                                <div class="col-6">
                                                    <label class="form-label required fs-8 fw-bold">Tipe WO</label>
                                                    <select wire:model="wo_type" class="form-select form-select-sm form-select-solid fs-8">
                                                        <option value="plan">Plan (PM Plan)</option>
                                                        <option value="breakdown">Breakdown (BD)</option>
                                                        <option value="corrective">Corrective (CM)</option>
                                                        <option value="preventive">Preventive (PM)</option>
                                                        <option value="inspection">Inspection</option>
                                                        <option value="overhaul">Overhaul</option>
                                                    </select>
                                                    @error('wo_type') <span class="text-danger fs-9 mt-1 d-block">{{ $message }}</span> @enderror
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label required fs-8 fw-bold">Prioritas</label>
                                                    <select wire:model="priority" class="form-select form-select-sm form-select-solid fs-8">
                                                        <option value="low">Low</option>
                                                        <option value="medium">Medium</option>
                                                        <option value="high">High</option>
                                                        <option value="emergency">Emergency</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label required fs-8 fw-bold">Status Unit Saat Ini</label>
                                                <select wire:model.live="unit_status" class="form-select form-select-sm form-select-solid fs-8">
                                                    <option value="breakdown">Breakdown (BD)</option>
                                                    <option value="ready">Ready Operasi</option>
                                                    <option value="in_progress">In Progress / Servis</option>
                                                    <option value="standby">Standby</option>
                                                    <option value="scheduled_maintenance">Scheduled PM</option>
                                                    <option value="accident">Accident</option>
                                                </select>
                                                @error('unit_status') <span class="text-danger fs-9 mt-1 d-block">{{ $message }}</span> @enderror
                                            </div>
                                            <!-- Opportunity Job Check Banner -->
                                            <div class="p-2.5 bg-light-warning bg-opacity-40 border border-warning border-opacity-30 rounded-2 d-flex align-items-center justify-content-between">
                                                <div class="d-flex flex-column pe-2">
                                                    <span class="fs-8 fw-bold text-gray-900">Opportunity Maintenance</span>
                                                    <span class="fs-9 text-muted">Dikerjakan saat unit standby/istirahat</span>
                                                </div>
                                                <div class="form-check form-switch form-check-custom form-check-solid mb-0">
                                                    <input class="form-check-input h-20px w-35px cursor-pointer" type="checkbox" wire:model="is_opportunity" id="isOpportunityFormCheck" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card 3: Timeline & Durasi Downtime -->
                                <div class="col-lg-4">
                                    <div class="card h-100 border border-gray-300 shadow-xs rounded-3 bg-white">
                                        <div class="card-header py-3 px-4 min-h-auto bg-light-danger border-bottom d-flex align-items-center">
                                            <div class="symbol symbol-30px symbol-circle bg-danger text-white me-2.5 d-flex align-items-center justify-content-center">
                                                <i class="ki-outline ki-time fs-5 text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="fs-7 fw-bolder text-gray-900 mb-0">3. Waktu & Durasi Downtime</h6>
                                                <span class="text-muted fs-9">Pencatatan jam breakdown & ready</span>
                                            </div>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="mb-3">
                                                <label class="form-label required fs-8 fw-bold text-danger">
                                                    <i class="ki-outline ki-time fs-7 text-danger me-1"></i> Tanggal & Jam Breakdown
                                                </label>
                                                <input type="datetime-local" wire:model="breakdown_at" class="form-control form-control-sm form-control-solid fs-8 border-danger border-opacity-25" required />
                                                <span class="text-muted fs-9">Waktu unit berhenti beroperasi</span>
                                                @error('breakdown_at') <span class="text-danger fs-9 mt-1 d-block">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label fs-8 fw-bold text-success">
                                                    <i class="ki-outline ki-check-circle fs-7 text-success me-1"></i> Tanggal & Jam Ready
                                                </label>
                                                <input type="datetime-local" wire:model.live="ready_at" class="form-control form-control-sm form-control-solid fs-8 border-success border-opacity-25" />
                                                <span class="text-muted fs-9">Wajib diisi jika Status Unit = Ready</span>
                                                @error('ready_at') <span class="text-danger fs-9 mt-1 d-block">{{ $message }}</span> @enderror
                                            </div>

                                            @if(!empty($ready_at) && !empty($breakdown_at) && strtotime($ready_at) >= strtotime($breakdown_at))
                                                @php
                                                    $dtHours = round((strtotime($ready_at) - strtotime($breakdown_at)) / 3600, 2);
                                                @endphp
                                                <div class="p-2 mt-2 bg-light-success border border-success border-opacity-25 rounded-2 d-flex align-items-center justify-content-between">
                                                    <span class="fs-9 fw-semibold text-gray-700">Kalkulasi Downtime:</span>
                                                    <span class="badge badge-success fs-9 fw-bold">{{ $dtHours }} Jam</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="separator my-6"></div>

                            <!-- Bagian 2: Tasks (Problems) & Subtasks (Actions) -->
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-30px symbol-circle bg-primary text-white me-2.5 d-flex align-items-center justify-content-center">
                                        <i class="ki-outline ki-element-11 fs-5 text-white"></i>
                                    </div>
                                    <div>
                                        <h5 class="fs-6 fw-bolder text-gray-900 mb-0">Daftar Problem (Task) & Action (Subtask)</h5>
                                        <span class="text-muted fs-9">Kelola hierarki kerusakan, urutan pengerjaan, dan alokasi antar problem</span>
                                    </div>
                                </div>
                                <button type="button" wire:click="addTask" class="btn btn-sm btn-light-primary fw-bold">
                                    <i class="ki-outline ki-plus fs-5 me-1"></i> Tambah Problem Baru
                                </button>
                            </div>

                            @foreach($tasks as $tIdx => $task)
                                <div class="card card-bordered mb-5 shadow-sm border-2 {{ $loop->first ? 'border-primary' : 'border-gray-300' }} bg-white rounded-3">
                                    <!-- Task / Problem Header -->
                                    <div class="card-header py-2.5 px-4 min-h-45px {{ $loop->first ? 'bg-light-primary' : 'bg-light' }} rounded-top-3 border-bottom d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            @if($loop->first)
                                                <span class="badge badge-danger fw-bold fs-8">PRIMARY PROBLEM</span>
                                            @else
                                                <span class="badge badge-dark fw-bold fs-8">PROBLEM #{{ $loop->iteration }}</span>
                                            @endif
                                            <span class="fw-bold text-gray-800 fs-7">
                                                {{ !empty($task['problem_title']) ? \Illuminate\Support\Str::limit($task['problem_title'], 45) : 'Deskripsi Masalah / Kerusakan' }}
                                            </span>
                                            <span class="badge badge-light-secondary text-gray-700 fs-9 py-0.5 px-2">
                                                {{ count($task['subtasks']) }} Action
                                            </span>
                                        </div>

                                        <div class="d-flex align-items-center gap-1">
                                            <!-- Move Task Up / Down Buttons -->
                                            <div class="btn-group btn-group-sm me-1">
                                                <button
                                                    type="button"
                                                    wire:click="moveTaskUp('{{ $tIdx }}')"
                                                    @if($loop->first) disabled @endif
                                                    class="btn btn-xs btn-icon btn-light"
                                                    title="Geser Problem ke Atas (Prioritas Lebih Tinggi)"
                                                >
                                                    <i class="ki-outline ki-arrow-up fs-5 {{ $loop->first ? 'text-muted' : 'text-primary' }}"></i>
                                                </button>
                                                <button
                                                    type="button"
                                                    wire:click="moveTaskDown('{{ $tIdx }}')"
                                                    @if($loop->last) disabled @endif
                                                    class="btn btn-xs btn-icon btn-light"
                                                    title="Geser Problem ke Bawah"
                                                >
                                                    <i class="ki-outline ki-arrow-down fs-5 {{ $loop->last ? 'text-muted' : 'text-primary' }}"></i>
                                                </button>
                                            </div>

                                            @if(count($tasks) > 1)
                                                <button
                                                    type="button"
                                                    wire:click="removeTask('{{ $tIdx }}')"
                                                    class="btn btn-xs btn-icon btn-light-danger"
                                                    title="Hapus Problem Ini Beserta Seluruh Actionnya"
                                                >
                                                    <i class="ki-outline ki-trash fs-5"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="card-body p-4">
                                        <!-- Problem Title, Component & Timestamps Input -->
                                        <div class="row g-3 mb-4">
                                            <div class="col-md-5">
                                                <label class="form-label required fs-8 fw-bold text-gray-700">Problem (Gejala / Kerusakan)</label>
                                                <input
                                                    type="text"
                                                    wire:model="tasks.{{ $tIdx }}.problem_title"
                                                    class="form-control form-control-solid fs-7"
                                                    placeholder="Contoh: Bocor Oli pada Hose Main Pump / Engine Overheating"
                                                />
                                                @error("tasks.{$tIdx}.problem_title")
                                                    <span class="text-danger fs-8 mt-1 d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fs-8 fw-bold text-gray-700">Komponen</label>
                                                <select wire:model="tasks.{{ $tIdx }}.component" class="form-select form-select-solid fs-8">
                                                    <option value="">-- Pilih Komponen --</option>
                                                    @foreach($availableComponents as $cmp)
                                                        <option value="{{ $cmp->name }}">{{ $cmp->name }} ({{ $cmp->category }})</option>
                                                    @endforeach
                                                </select>
                                                @error("tasks.{$tIdx}.component")
                                                    <span class="text-danger fs-9 mt-1 d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fs-8 fw-bold text-gray-700">Waktu BD Problem</label>
                                                <input type="datetime-local" wire:model="tasks.{{ $tIdx }}.breakdown_at" class="form-control form-control-solid fs-8" />
                                                @error("tasks.{$tIdx}.breakdown_at")
                                                    <span class="text-danger fs-9 mt-1 d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label fs-8 fw-bold text-gray-700">Waktu Ready Problem</label>
                                                <input type="datetime-local" wire:model="tasks.{{ $tIdx }}.ready_at" class="form-control form-control-solid fs-8" />
                                                @error("tasks.{$tIdx}.ready_at")
                                                    <span class="text-danger fs-9 mt-1 d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Subtasks (Actions) Container -->
                                        <div class="ps-3 border-start border-3 border-primary">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h6 class="fs-8 fw-bold text-primary mb-0 text-uppercase">
                                                    <i class="ki-outline ki-abstract-26 fs-6 text-primary me-1"></i> Subtask / Actions (Tindakan Perbaikan untuk Problem Ini)
                                                </h6>
                                                <button type="button" wire:click="addSubtask('{{ $tIdx }}')" class="btn btn-xs btn-light-info fw-semibold py-1">
                                                    <i class="ki-outline ki-plus fs-6 me-1"></i> Tambah Action
                                                </button>
                                            </div>

                                            @foreach($task['subtasks'] as $sIdx => $subtask)
                                                <div class="card border border-gray-300 mb-3 bg-white shadow-xs rounded-2">
                                                    <div class="card-header py-2 px-3 min-h-35px bg-light-secondary border-bottom d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="badge badge-light-primary fs-9 fw-bold">Action #{{ $loop->iteration }}</span>
                                                            <span class="fs-9 text-gray-700 fw-semibold text-truncate" style="max-width: 250px;">
                                                                {{ !empty($subtask['action_title']) ? $subtask['action_title'] : 'Tindakan / Action Baru' }}
                                                            </span>
                                                        </div>

                                                        <div class="d-flex align-items-center gap-1">
                                                            <!-- Geser Urutan Action Up / Down dalam Task -->
                                                            <div class="btn-group btn-group-sm me-1">
                                                                <button
                                                                    type="button"
                                                                    wire:click="moveSubtaskUp('{{ $tIdx }}', '{{ $sIdx }}')"
                                                                    @if($loop->first) disabled @endif
                                                                    class="btn btn-xs btn-icon btn-light"
                                                                    style="width: 24px; height: 24px;"
                                                                    title="Geser Action ke Atas"
                                                                >
                                                                    <i class="ki-outline ki-arrow-up fs-7 {{ $loop->first ? 'text-muted' : 'text-primary' }}"></i>
                                                                </button>
                                                                <button
                                                                    type="button"
                                                                    wire:click="moveSubtaskDown('{{ $tIdx }}', '{{ $sIdx }}')"
                                                                    @if($loop->last) disabled @endif
                                                                    class="btn btn-xs btn-icon btn-light"
                                                                    style="width: 24px; height: 24px;"
                                                                    title="Geser Action ke Bawah"
                                                                >
                                                                    <i class="ki-outline ki-arrow-down fs-7 {{ $loop->last ? 'text-muted' : 'text-primary' }}"></i>
                                                                </button>
                                                            </div>

                                                            <!-- Pindahkan Action ke Problem Lain (Cross-Task Move) -->
                                                            @if(count($tasks) > 1)
                                                                <div class="dropdown d-inline-block">
                                                                    <button class="btn btn-xs btn-light-primary dropdown-toggle py-0.5 px-2 fs-9" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Pindahkan tindakan ini ke Problem lain">
                                                                        <i class="ki-outline ki-arrows-loop fs-8 me-1"></i> Pindah Problem
                                                                    </button>
                                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm fs-9 py-1">
                                                                        <li class="dropdown-header text-muted fs-9 py-1 px-3">Pindahkan Action ini ke:</li>
                                                                        @foreach($tasks as $targetTIdx => $targetTask)
                                                                            @if($targetTIdx !== $tIdx)
                                                                                <li>
                                                                                    <a class="dropdown-item py-1.5 px-3 cursor-pointer" href="javascript:void(0)" wire:click="moveSubtaskToTask('{{ $tIdx }}', '{{ $sIdx }}', '{{ $targetTIdx }}')">
                                                                                        <span class="badge {{ $targetTIdx === 0 ? 'badge-light-danger' : 'badge-light-dark' }} fs-9 me-1">
                                                                                            {{ $targetTIdx === 0 ? 'Primary' : '#' . ($targetTIdx + 1) }}
                                                                                        </span>
                                                                                        {{ \Illuminate\Support\Str::limit($targetTask['problem_title'] ?: 'Problem #' . ($targetTIdx + 1), 22) }}
                                                                                    </a>
                                                                                </li>
                                                                            @endif
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endif

                                                            @if(count($task['subtasks']) > 1)
                                                                <button
                                                                    type="button"
                                                                    wire:click="removeSubtask('{{ $tIdx }}', '{{ $sIdx }}')"
                                                                    class="btn btn-xs btn-icon btn-light-danger py-0"
                                                                    style="width: 24px; height: 24px;"
                                                                    title="Hapus Action"
                                                                >
                                                                    <i class="ki-outline ki-cross fs-6"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="card-body p-3">

                                                        <!-- Action Title Input -->
                                                        <div class="mb-3">
                                                            <label class="form-label required fs-9 fw-bold text-gray-700">Judul Pekerjaan / Tindakan (Action Title)</label>
                                                            <input
                                                                type="text"
                                                                wire:model="tasks.{{ $tIdx }}.subtasks.{{ $sIdx }}.action_title"
                                                                class="form-control form-control-sm form-control-solid fs-8"
                                                                placeholder="Contoh: Bongkar Hose lama, Ganti O-Ring & Pasang Hose baru"
                                                            />
                                                            @error("tasks.{$tIdx}.subtasks.{$sIdx}.action_title")
                                                                <span class="text-danger fs-9 mt-1 d-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>

                                                        <!-- PIC & Mekanik Penugasan per Subtask -->
                                                        <div class="row g-2 mb-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label fs-9 fw-bold text-gray-700">PIC Subtask / Leader</label>
                                                                <select wire:model="tasks.{{ $tIdx }}.subtasks.{{ $sIdx }}.assigned_to_id" class="form-select form-select-sm form-select-solid fs-8">
                                                                    <option value="">-- Pilih PIC --</option>
                                                                    @foreach($users as $u)
                                                                        <option value="{{ $u->id }}">{{ $u->full_name }} ({{ $u->position->name ?? 'Staff' }})</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fs-9 fw-bold text-gray-700">Tim Mekanik / Personel Terlibat</label>
                                                                <select wire:model="tasks.{{ $tIdx }}.subtasks.{{ $sIdx }}.mechanic_ids" multiple class="form-select form-select-sm form-select-solid fs-8" style="height: 65px;">
                                                                    @foreach($users as $u)
                                                                        <option value="{{ $u->id }}">{{ $u->full_name }} ({{ $u->position->name ?? 'Mekanik' }})</option>
                                                                    @endforeach
                                                                </select>
                                                                <span class="text-muted fs-9">Tahan Ctrl / Cmd untuk memilih banyak personel.</span>
                                                            </div>
                                                        </div>

                                                        <!-- Timeline & Kendala (Bottleneck / Delay Reason) -->
                                                        <div class="row g-2 mb-3 p-2 bg-light-warning bg-opacity-30 rounded-2 border border-warning border-opacity-30">
                                                            <div class="col-md-3">
                                                                <label class="form-label fs-9 fw-bold text-gray-700">Waktu Mulai Action</label>
                                                                <input type="datetime-local" wire:model="tasks.{{ $tIdx }}.subtasks.{{ $sIdx }}.breakdown_at" class="form-control form-control-sm fs-9" />
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label fs-9 fw-bold text-gray-700">Waktu Selesai Action</label>
                                                                <input type="datetime-local" wire:model="tasks.{{ $tIdx }}.subtasks.{{ $sIdx }}.ready_at" class="form-control form-control-sm fs-9" />
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label fs-9 fw-bold text-gray-700">Parameter Kendala (Delay Reason)</label>
                                                                <select wire:model="tasks.{{ $tIdx }}.subtasks.{{ $sIdx }}.obstacle" class="form-select form-select-sm fs-9">
                                                                    <option value="none">Lancar (Tanpa Kendala)</option>
                                                                    <option value="waiting_part">Menunggu Sparepart</option>
                                                                    <option value="waiting_manpower">Menunggu Mekanik</option>
                                                                    <option value="waiting_tool">Menunggu Special Tool / Crane</option>
                                                                    <option value="waiting_weather">Kendala Cuaca / Hujan</option>
                                                                    <option value="waiting_approval">Menunggu Approval / PO</option>
                                                                    <option value="waiting_location">Kendala Lokasi / Akses</option>
                                                                    <option value="waiting_external">Vendor Luar / Bengkel Luar</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <label class="form-label fs-9 fw-bold text-gray-700">Rincian / Catatan Kendala</label>
                                                                <input type="text" wire:model="tasks.{{ $tIdx }}.subtasks.{{ $sIdx }}.obstacle_notes" class="form-control form-control-sm fs-9" placeholder="Rincian kendala jika ada delay" />
                                                            </div>
                                                        </div>

                                                        <!-- Spareparts Per Subtask -->
                                                        <div class="p-3 bg-light rounded-2 border">
                                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                                <span class="fs-9 fw-bold text-gray-800">
                                                                    <i class="ki-outline ki-cube-2 fs-7 text-primary me-1"></i> Kebutuhan Suku Cadang (Sparepart Action Ini)
                                                                </span>
                                                                <button type="button" wire:click="addSparepart('{{ $tIdx }}', '{{ $sIdx }}')" class="btn btn-xs btn-light-success py-0 px-2 fs-9">
                                                                    <i class="ki-outline ki-plus fs-8 me-1"></i> Tambah Part
                                                                </button>
                                                            </div>

                                                            @if(!empty($subtask['spareparts']) && count($subtask['spareparts']) > 0)
                                                                <div class="table-responsive">
                                                                    <table class="table table-bordered table-sm gs-2 gy-1 fs-9 bg-white mb-0">
                                                                        <thead class="bg-light fw-bold text-gray-700">
                                                                            <tr>
                                                                                <th style="min-width: 170px;">Pilih Master Suku Cadang</th>
                                                                                <th style="min-width: 110px;">Part Number</th>
                                                                                <th style="min-width: 140px;">Nama Part</th>
                                                                                <th style="width: 65px;">Qty</th>
                                                                                <th style="width: 70px;">Satuan</th>
                                                                                <th style="width: 125px;">Ketersediaan Stok</th>
                                                                                <th style="width: 105px;">Tipe Aksi</th>
                                                                                <th style="width: 105px;">Status</th>
                                                                                <th style="width: 30px;"></th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($subtask['spareparts'] as $pIdx => $part)
                                                                                <tr>
                                                                                    <td>
                                                                                        <select wire:model.live="tasks.{{ $tIdx }}.subtasks.{{ $sIdx }}.spareparts.{{ $pIdx }}.part_id" class="form-select form-select-sm fs-9 p-1">
                                                                                            <option value="">-- Manual / Cari Master --</option>
                                                                                            @foreach($masterParts as $mp)
                                                                                                @php
                                                                                                    $locText = $mp->locations && $mp->locations->count() > 0 
                                                                                                        ? $mp->locations->map(fn($l) => ($l->site->site_name ?? 'Gudang') . ': ' . $l->rack_location)->implode(', ') 
                                                                                                        : ($mp->bin_location ?: 'Rak A-01');
                                                                                                @endphp
                                                                                                <option value="{{ $mp->id }}">
                                                                                                    {{ $mp->part_number }} - {{ $mp->name }} (Stok: {{ number_format($mp->stock_on_hand, 0) }} | {{ $locText }})
                                                                                                </option>
                                                                                            @endforeach
                                                                                        </select>
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="text" wire:model="tasks.{{ $tIdx }}.subtasks.{{ $sIdx }}.spareparts.{{ $pIdx }}.part_number" class="form-control form-control-sm fs-9 p-1" placeholder="P/N: 123-456" />
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="text" wire:model="tasks.{{ $tIdx }}.subtasks.{{ $sIdx }}.spareparts.{{ $pIdx }}.part_name" class="form-control form-control-sm fs-9 p-1" placeholder="Nama Suku Cadang" />
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="number" step="any" min="0.01" wire:model.live.debounce.300ms="tasks.{{ $tIdx }}.subtasks.{{ $sIdx }}.spareparts.{{ $pIdx }}.quantity" class="form-control form-control-sm fs-9 p-1 text-center" />
                                                                                    </td>
                                                                                    <td>
                                                                                        <input type="text" wire:model="tasks.{{ $tIdx }}.subtasks.{{ $sIdx }}.spareparts.{{ $pIdx }}.unit" class="form-control form-control-sm fs-9 p-1 text-center" />
                                                                                    </td>
                                                                                    <td class="text-center align-middle">
                                                                                        @if(!empty($part['part_id']))
                                                                                            @if(($part['stock_available'] ?? 0) >= ($part['quantity'] ?? 1) && ($part['stock_available'] ?? 0) > 0)
                                                                                                <span class="badge badge-light-success fs-9 py-1 px-1.5 d-block text-truncate">
                                                                                                    <i class="ki-outline ki-check-circle fs-8 text-success me-1"></i> Ready ({{ $part['stock_available'] }})
                                                                                                </span>
                                                                                            @else
                                                                                                <span class="badge badge-light-danger fs-9 py-1 px-1.5 d-block text-truncate" title="Stok gudang tidak cukup. Akan otomatis dibuatkan MOL ke SCM!">
                                                                                                    <i class="ki-outline ki-cross-circle fs-8 text-danger me-1"></i> Habis (Auto MOL)
                                                                                                </span>
                                                                                            @endif
                                                                                        @else
                                                                                            <span class="text-muted fs-9">-</span>
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>
                                                                                        <select wire:model="tasks.{{ $tIdx }}.subtasks.{{ $sIdx }}.spareparts.{{ $pIdx }}.action_type" class="form-select form-select-sm fs-9 p-1">
                                                                                            <option value="replace">Ganti Baru</option>
                                                                                            <option value="swap">Swap / Kanibal</option>
                                                                                            <option value="repair">Perbaiki Part</option>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td>
                                                                                        <select wire:model="tasks.{{ $tIdx }}.subtasks.{{ $sIdx }}.spareparts.{{ $pIdx }}.status" class="form-select form-select-sm fs-9 p-1">
                                                                                            <option value="installed">Terpasang</option>
                                                                                            <option value="waiting_part">Waiting Part</option>
                                                                                            <option value="cancelled">Batal</option>
                                                                                        </select>
                                                                                    </td>
                                                                                    <td class="text-center align-middle">
                                                                                        <button type="button" wire:click="removeSparepart('{{ $tIdx }}', '{{ $sIdx }}', '{{ $pIdx }}')" class="btn btn-xs btn-icon btn-light-danger p-0" style="width: 20px; height: 20px;">
                                                                                            <i class="ki-outline ki-cross fs-7"></i>
                                                                                        </button>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @else
                                                                <div class="text-muted fs-9 fst-italic">Belum ada part untuk action ini. Klik "+ Tambah Part" jika diperlukan penggantian suku cadang.</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="separator my-6"></div>

                            <!-- Bagian 3: Card Foto & Lampiran -->
                            <div class="card border border-gray-300 shadow-xs rounded-3 bg-white mb-4">
                                <div class="card-header py-3 px-4 min-h-auto bg-light-secondary border-bottom d-flex align-items-center">
                                    <div class="symbol symbol-30px symbol-circle bg-dark text-white me-2.5 d-flex align-items-center justify-content-center">
                                        <i class="ki-outline ki-camera fs-5 text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="fs-7 fw-bolder text-gray-900 mb-0">Foto Kondisi Unit & Dokumen Lampiran</h6>
                                        <span class="text-muted fs-9">Dokumentasi before repair dan checklist form pendukung</span>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fs-8 fw-bold text-gray-700">Foto Sebelum Perbaikan (Before Repair)</label>
                                            <input type="file" wire:model="before_photo_file" class="form-control form-control-sm form-control-solid fs-8" accept="image/*" />
                                            <span class="text-muted fs-9">Format gambar: JPG, PNG (Maks 10MB)</span>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fs-8 fw-bold text-gray-700">Dokumen Pendukung / Checklist (PDF / Doc)</label>
                                            <input type="file" wire:model="attachment_doc_file" class="form-control form-control-sm form-control-solid fs-8" />
                                            <span class="text-muted fs-9">Lampiran form BAP, checklist fisik, atau scan WO</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-top py-3 px-6">
                            <button type="button" wire:click="$set('showFormModal', false)" class="btn btn-light fs-7">Batal</button>
                            <button type="submit" class="btn btn-primary fs-7 fw-bold">
                                <i class="ki-outline ki-check fs-4 me-1"></i> Simpan Work Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Form-->

    <!--begin::Modal Selesaikan Pengerjaan (Complete WO)-->
    @if($showCompleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered mw-550px">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header border-bottom py-4 px-6 bg-light-success">
                        <h4 class="modal-title fw-bolder text-gray-900">
                            <i class="ki-outline ki-check-circle fs-2 text-success me-2"></i> Konfirmasi Selesaikan Pekerjaan
                        </h4>
                        <button type="button" wire:click="$set('showCompleteModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <form wire:submit="submitCompleteWork">
                        <div class="modal-body py-5 px-6">
                            <div class="mb-4">
                                <label class="form-label required fs-7 fw-bold">Ringkasan Tindakan Akhir (Action Taken)</label>
                                <textarea wire:model="complete_action_taken" class="form-control form-control-solid fs-7" rows="3" placeholder="Jelaskan tindakan akhir yang telah dilakukan..."></textarea>
                                @error('complete_action_taken') <span class="text-danger fs-8 mt-1 d-block">{{ $message }}</span> @enderror
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Penyebab Kerusakan (Root Cause)</label>
                                    <select wire:model="complete_root_cause" class="form-select form-select-solid fs-7">
                                        <option value="Wear & Tear">Wear & Tear (Aus Wajar)</option>
                                        <option value="Operator Error">Operator Error</option>
                                        <option value="Lack of Lubrication">Kurang Pelumasan</option>
                                        <option value="Contamination">Kontaminasi Oli/Bahan Bakar</option>
                                        <option value="Fatigue & Stress">Fatigue / Keretakan Struktur</option>
                                        <option value="Electrical Issue">Kelistrikan / Sensor</option>
                                        <option value="Other">Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-7 fw-bold">Total Jam Kerja Mekanik (Hours)</label>
                                    <input type="number" step="0.5" wire:model="complete_total_labor_hours" class="form-control form-control-solid fs-7" />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fs-7 fw-bold">Foto Sesudah Perbaikan (After)</label>
                                <input type="file" wire:model="after_photo_file" class="form-control form-control-solid fs-7" accept="image/*" />
                            </div>
                        </div>

                        <div class="modal-footer border-top py-3 px-6">
                            <button type="button" wire:click="$set('showCompleteModal', false)" class="btn btn-light fs-7">Batal</button>
                            <button type="submit" class="btn btn-success fs-7 fw-bold">
                                <i class="ki-outline ki-check fs-4 me-1"></i> Selesaikan Work Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Selesaikan Pengerjaan-->

    <!--begin::Modal Detail WO Hierarki-->
    @if($showDetailModal && $selectedWorkOrder)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.6); z-index: 1055;">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 95vw; width: 95vw; margin: 1.5rem auto;">
                <div class="modal-content rounded-4 border-0 shadow-lg" style="height: 92vh;">
                    <div class="modal-header border-bottom py-4 px-6 bg-light">
                        <div class="d-flex align-items-center">
                            <h4 class="modal-title fw-bolder text-gray-900 me-3">
                                {{ $selectedWorkOrder->wo_number }}
                            </h4>
                            <span class="badge {{ $selectedWorkOrder->status_badge['class'] }} fs-8 fw-bold">
                                {{ $selectedWorkOrder->status_badge['label'] }}
                            </span>
                        </div>
                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-sm btn-icon btn-light">
                            <i class="ki-outline ki-cross fs-4"></i>
                        </button>
                    </div>

                    <div class="modal-body py-5 px-7 overflow-y-auto flex-grow-1">
                        <!-- Asset & Unit Info Card -->
                        <div class="card bg-light-primary rounded-3 p-4 mb-4 border border-primary border-opacity-25">
                            <div class="row g-3">
                                <div class="col-sm-3">
                                    <div class="fs-8 text-muted">Nomor Unit & Status</div>
                                    <div class="d-flex align-items-center gap-1 mt-1">
                                        <span class="fs-6 fw-bolder text-gray-900">{{ $selectedWorkOrder->equipment->unit ?? 'N/A' }}</span>
                                        <span class="badge {{ $selectedWorkOrder->unit_status_badge['class'] }} fs-9 py-0.5 px-1.5">
                                            {{ $selectedWorkOrder->unit_status_badge['label'] }}
                                        </span>
                                    </div>
                                    @if($selectedWorkOrder->is_opportunity)
                                        <span class="badge badge-light-warning fs-9 fw-bold mt-1">
                                            <i class="ki-outline ki-flash fs-8 text-warning me-1"></i> Opportunity Job
                                        </span>
                                    @endif
                                </div>
                                <div class="col-sm-3">
                                    <div class="fs-8 text-muted">Model & Lokasi Site</div>
                                    <div class="fs-6 fw-bold text-gray-900">{{ $selectedWorkOrder->equipment->reffEquip->model ?? '-' }}</div>
                                    <span class="text-muted fs-8">{{ $selectedWorkOrder->site->site_name ?? '-' }}</span>
                                </div>
                                <div class="col-sm-3">
                                    <div class="fs-8 text-muted">Waktu Breakdown Unit</div>
                                    <div class="fs-7 fw-bold text-danger">
                                        <i class="ki-outline ki-time fs-6 text-danger me-1"></i>
                                        {{ $selectedWorkOrder->breakdown_at ? $selectedWorkOrder->breakdown_at->format('d M Y H:i') : '-' }}
                                    </div>
                                    <div class="fs-9 text-muted">HM: {{ $selectedWorkOrder->current_hm ?? 0 }} hrs | KM: {{ $selectedWorkOrder->current_km ?? 0 }}</div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="fs-8 text-muted">Waktu Ready Unit</div>
                                    <div class="fs-7 fw-bold {{ $selectedWorkOrder->ready_at ? 'text-success' : 'text-gray-600' }}">
                                        <i class="ki-outline ki-check-circle fs-6 {{ $selectedWorkOrder->ready_at ? 'text-success' : 'text-gray-400' }} me-1"></i>
                                        {{ $selectedWorkOrder->ready_at ? $selectedWorkOrder->ready_at->format('d M Y H:i') : 'Belum Ready' }}
                                    </div>
                                    @if($selectedWorkOrder->downtime_hours > 0)
                                        <span class="badge badge-light-danger fs-9 fw-bold mt-1">
                                            Total Downtime: {{ $selectedWorkOrder->downtime_hours }} Jam
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Hierarki Problems & Actions Tree -->
                        <h5 class="fs-6 fw-bold text-gray-900 mb-3">
                            <i class="ki-outline ki-element-11 fs-4 text-primary me-1"></i> Rincian Task (Problem) & Subtask (Action)
                        </h5>

                        @foreach($selectedWorkOrder->tasks as $t)
                            <div class="card card-bordered mb-4 border-2 {{ $t->is_primary ? 'border-primary' : 'border-gray-300' }} rounded-3 shadow-xs">
                                <div class="card-header py-2 px-4 bg-light d-flex align-items-center justify-content-between min-h-40px">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($t->is_primary)
                                            <span class="badge badge-danger fw-bold fs-9">PRIMARY PROBLEM</span>
                                        @else
                                            <span class="badge badge-light-dark fw-bold fs-9">TASK #{{ $loop->iteration }}</span>
                                        @endif
                                        @if($t->component)
                                            <span class="badge badge-light-info text-info fw-bold fs-9">
                                                <i class="ki-outline ki-setting-2 fs-9 text-info me-0.5"></i> {{ $t->component }}
                                            </span>
                                        @endif
                                        <span class="fw-bold text-gray-900 fs-7">{{ $t->problem_title }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($t->breakdown_at)
                                            <span class="text-muted fs-9">
                                                {{ $t->breakdown_at->format('d/m H:i') }} ➔ {{ $t->ready_at ? $t->ready_at->format('d/m H:i') : 'Ongoing' }}
                                            </span>
                                        @endif
                                        <span class="badge {{ $t->status_badge['class'] }} fs-9">{{ $t->status_badge['label'] }}</span>
                                    </div>
                                </div>

                                <div class="card-body p-3">
                                    @foreach($t->subtasks as $st)
                                        <div class="card border border-gray-200 mb-3 bg-white rounded-2">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <span class="fs-7 fw-bold text-primary">
                                                        <i class="ki-outline ki-arrow-right fs-5 text-primary me-1"></i> Action: {{ $st->action_title }}
                                                    </span>
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if($st->obstacle && $st->obstacle !== 'none')
                                                            <span class="badge {{ $st->obstacle_badge['class'] }} fs-9">
                                                                <i class="ki-outline ki-information-2 fs-9 me-1"></i> {{ $st->obstacle_badge['label'] }}
                                                            </span>
                                                        @endif
                                                        <span class="badge {{ $st->status_badge['class'] }} fs-9">{{ $st->status_badge['label'] }}</span>
                                                    </div>
                                                </div>

                                                <!-- Timeline Subtask & Obstacle Notes -->
                                                <div class="d-flex flex-wrap align-items-center gap-3 mb-2 fs-9 text-muted bg-light p-2 rounded-2">
                                                    @if($st->breakdown_at)
                                                        <div>
                                                            <strong class="text-gray-700">Mulai:</strong> {{ $st->breakdown_at->format('d/m/Y H:i') }}
                                                        </div>
                                                    @endif
                                                    @if($st->ready_at)
                                                        <div>
                                                            <strong class="text-gray-700">Selesai:</strong> {{ $st->ready_at->format('d/m/Y H:i') }}
                                                        </div>
                                                    @endif
                                                    @if($st->obstacle_notes)
                                                        <div>
                                                            <strong class="text-danger">Catatan Kendala:</strong> {{ $st->obstacle_notes }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Personel di Subtask -->
                                                <div class="d-flex flex-wrap gap-2 mb-2">
                                                    @if($st->assignedTo)
                                                        <span class="badge badge-light-info fs-8">
                                                            <i class="ki-outline ki-crown fs-6 text-info me-1"></i> PIC: {{ $st->assignedTo->full_name }}
                                                        </span>
                                                    @endif
                                                    @foreach($st->mechanics as $m)
                                                        <span class="badge badge-light-secondary fs-8">
                                                            <i class="ki-outline ki-user fs-6 text-gray-600 me-1"></i> {{ $m->full_name }}
                                                        </span>
                                                    @endforeach
                                                </div>

                                                <!-- Spareparts di Subtask -->
                                                @if($st->spareparts->count() > 0)
                                                    <div class="mt-2">
                                                        <span class="fs-9 fw-bold text-gray-700 mb-1 d-block">Suku Cadang / Spareparts Terkait:</span>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-sm gs-2 gy-1 fs-9 mb-0">
                                                                <thead class="bg-light fw-bold text-gray-700">
                                                                    <tr>
                                                                        <th>Part Number</th>
                                                                        <th>Nama Part</th>
                                                                        <th>Qty</th>
                                                                        <th>Tipe Aksi</th>
                                                                        <th>Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($st->spareparts as $part)
                                                                        <tr>
                                                                            <td class="fw-bold">{{ $part->part_number }}</td>
                                                                            <td>{{ $part->part_name }}</td>
                                                                            <td>{{ $part->quantity }} {{ $part->unit }}</td>
                                                                            <td>
                                                                                <span class="badge {{ $part->action_type_badge['class'] }} fs-9">
                                                                                    {{ $part->action_type_badge['label'] }}
                                                                                </span>
                                                                            </td>
                                                                            <td>
                                                                                <span class="badge {{ $part->status_badge['class'] }} fs-9">
                                                                                    {{ $part->status_badge['label'] }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <!-- Photos Before & After -->
                        @if($selectedWorkOrder->before_photo || $selectedWorkOrder->after_photo)
                            <div class="mb-3">
                                <h6 class="fs-7 fw-bold text-gray-800 mb-2">Dokumentasi Foto:</h6>
                                <div class="row g-3">
                                    @if($selectedWorkOrder->before_photo)
                                        <div class="col-6 text-center">
                                            <span class="fs-8 text-muted d-block mb-1">Sebelum Perbaikan (Before)</span>
                                            <a href="{{ asset('storage/'.$selectedWorkOrder->before_photo) }}" target="_blank">
                                                <img src="{{ asset('storage/'.$selectedWorkOrder->before_photo) }}" class="img-fluid rounded-3 border" style="max-height: 180px; object-fit: cover;" />
                                            </a>
                                        </div>
                                    @endif
                                    @if($selectedWorkOrder->after_photo)
                                        <div class="col-6 text-center">
                                            <span class="fs-8 text-muted d-block mb-1">Sesudah Perbaikan (After)</span>
                                            <a href="{{ asset('storage/'.$selectedWorkOrder->after_photo) }}" target="_blank">
                                                <img src="{{ asset('storage/'.$selectedWorkOrder->after_photo) }}" class="img-fluid rounded-3 border" style="max-height: 180px; object-fit: cover;" />
                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <!-- ========================================================== -->
                        <!-- 💬 DISKUSI & KOMUNIKASI WORK ORDER (Facebook Style)          -->
                        <!-- ========================================================== -->
                        <div class="separator my-5"></div>

                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="symbol symbol-30px symbol-circle bg-primary me-2.5 d-flex align-items-center justify-content-center">
                                    <i class="ki-outline ki-message-text-2 fs-5 text-white"></i>
                                </div>
                                <div>
                                    <h5 class="fs-6 fw-bolder text-gray-900 mb-0">Diskusi & Komunikasi</h5>
                                    <span class="text-muted fs-9">Obrolan tim terkait Work Order ini</span>
                                </div>
                            </div>

                            {{-- Input Komentar Baru --}}
                            <div class="d-flex align-items-start gap-3 mb-5">
                                <div class="symbol symbol-38px symbol-circle flex-shrink-0">
                                    <div class="symbol-label fs-7 fw-bolder bg-primary text-white">
                                        {{ strtoupper(substr(Auth::user()?->full_name ?? 'U', 0, 2)) }}
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="bg-light rounded-3 p-3 border">
                                        <textarea
                                            wire:model="newComment"
                                            class="form-control form-control-solid border-0 bg-transparent fs-7 p-0 resize-none"
                                            rows="2"
                                            placeholder="Tulis komentar atau update terkait Work Order ini... (Contoh: Sparepart sudah tiba, estimasi selesai 3 jam)"
                                        ></textarea>
                                    </div>
                                    @error('newComment')
                                        <span class="text-danger fs-9 mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                    <div class="d-flex justify-content-end mt-2">
                                        <button
                                            type="button"
                                            wire:click="postComment"
                                            class="btn btn-primary btn-sm fs-8 fw-bold"
                                        >
                                            <i class="ki-outline ki-send fs-6 me-1"></i> Kirim Komentar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- List Komentar --}}
                            @php $comments = $selectedWorkOrder->comments ?? collect(); @endphp

                            @if($comments->count() > 0)
                                <div class="d-flex flex-column gap-4">
                                    @foreach($comments as $comment)
                                        {{-- Post Utama --}}
                                        <div class="d-flex align-items-start gap-3" id="comment-{{ $comment->id }}">
                                            <div class="symbol symbol-38px symbol-circle flex-shrink-0">
                                                <div class="symbol-label fs-8 fw-bolder bg-info text-white">
                                                    {{ $comment->initials }}
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="bg-light-primary bg-opacity-50 rounded-3 px-4 py-3 border border-primary border-opacity-15">
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        <div>
                                                            <span class="fw-bolder text-gray-900 fs-7">{{ $comment->user?->full_name ?? 'User' }}</span>
                                                            <span class="text-muted fs-9 ms-2">{{ $comment->user?->position?->name ?? '' }}</span>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-2">
                                                            <span class="text-muted fs-9">{{ $comment->time_ago }}</span>
                                                            @if($comment->user_id === Auth::id())
                                                                <button
                                                                    type="button"
                                                                    wire:click="deleteComment('{{ $comment->id }}')"
                                                                    wire:confirm="Hapus komentar ini?"
                                                                    class="btn btn-xs btn-icon btn-light-danger"
                                                                    title="Hapus"
                                                                >
                                                                    <i class="ki-outline ki-trash fs-9"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <p class="text-gray-800 fs-7 mb-0" style="white-space: pre-wrap;">{{ $comment->body }}</p>
                                                </div>

                                                {{-- Tombol Balas --}}
                                                <div class="d-flex align-items-center gap-3 mt-1 ps-2">
                                                    <button
                                                        type="button"
                                                        wire:click="startReply('{{ $comment->id }}')"
                                                        class="btn btn-xs btn-link text-muted text-hover-primary p-0 fs-9 fw-semibold"
                                                    >
                                                        <i class="ki-outline ki-message-text-2 fs-9 me-1"></i>
                                                        {{ $replyingToId === $comment->id ? 'Batal Balas' : 'Balas' }}
                                                    </button>
                                                    @if($comment->replies->count() > 0)
                                                        <span class="text-muted fs-9">{{ $comment->replies->count() }} balasan</span>
                                                    @endif
                                                </div>

                                                {{-- Form Balas --}}
                                                @if($replyingToId === $comment->id)
                                                    <div class="d-flex align-items-start gap-2 mt-3 ms-2">
                                                        <div class="symbol symbol-30px symbol-circle flex-shrink-0">
                                                            <div class="symbol-label fs-9 fw-bolder bg-warning text-white">
                                                                {{ strtoupper(substr(Auth::user()?->full_name ?? 'U', 0, 2)) }}
                                                            </div>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="bg-light rounded-3 px-3 py-2 border">
                                                                <textarea
                                                                    wire:model="newReply"
                                                                    class="form-control form-control-solid border-0 bg-transparent fs-8 p-0 resize-none"
                                                                    rows="2"
                                                                    placeholder="Tulis balasan..."
                                                                    autofocus
                                                                ></textarea>
                                                            </div>
                                                            @error('newReply')
                                                                <span class="text-danger fs-9 mt-1 d-block">{{ $message }}</span>
                                                            @enderror
                                                            <div class="d-flex justify-content-end mt-1">
                                                                <button
                                                                    type="button"
                                                                    wire:click="postReply"
                                                                    class="btn btn-primary btn-xs fs-9 fw-bold"
                                                                >
                                                                    <i class="ki-outline ki-send fs-9 me-1"></i> Kirim Balasan
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Replies / Balasan --}}
                                                @if($comment->replies->count() > 0)
                                                    <div class="ms-4 mt-3 d-flex flex-column gap-3">
                                                        @foreach($comment->replies as $reply)
                                                            <div class="d-flex align-items-start gap-2">
                                                                <div class="symbol symbol-30px symbol-circle flex-shrink-0">
                                                                    <div class="symbol-label fs-9 fw-bolder bg-light-success text-success">
                                                                        {{ $reply->initials }}
                                                                    </div>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <div class="bg-white rounded-3 px-3 py-2 border border-gray-200">
                                                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                                                            <div>
                                                                                <span class="fw-bolder text-gray-900 fs-8">{{ $reply->user?->full_name ?? 'User' }}</span>
                                                                            </div>
                                                                            <div class="d-flex align-items-center gap-2">
                                                                                <span class="text-muted fs-9">{{ $reply->time_ago }}</span>
                                                                                @if($reply->user_id === Auth::id())
                                                                                    <button
                                                                                        type="button"
                                                                                        wire:click="deleteComment('{{ $reply->id }}')"
                                                                                        wire:confirm="Hapus balasan ini?"
                                                                                        class="btn btn-xs btn-icon btn-light-danger"
                                                                                        title="Hapus"
                                                                                    >
                                                                                        <i class="ki-outline ki-trash fs-9"></i>
                                                                                    </button>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                        <p class="text-gray-800 fs-8 mb-0" style="white-space: pre-wrap;">{{ $reply->body }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-6">
                                    <div class="symbol symbol-60px symbol-circle bg-light-primary mx-auto mb-3">
                                        <div class="symbol-label">
                                            <i class="ki-outline ki-message-text-2 fs-2x text-primary"></i>
                                        </div>
                                    </div>
                                    <p class="text-muted fs-7 mb-0">Belum ada diskusi untuk Work Order ini.</p>
                                    <p class="text-muted fs-9">Tulis komentar pertama untuk memulai obrolan tim!</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Modal Footer: Workflow Actions + Edit + Delete + Close --}}
                    <div class="modal-footer border-top py-3 px-6 d-flex justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if($selectedWorkOrder->status === 'open')
                                <button type="button" wire:click="startWork('{{ $selectedWorkOrder->id }}')" class="btn btn-warning btn-sm fs-7">
                                    <i class="ki-outline ki-time fs-4 me-1"></i> Mulai Pekerjaan
                                </button>
                            @elseif($selectedWorkOrder->status === 'in_progress')
                                <button type="button" wire:click="openCompleteModal('{{ $selectedWorkOrder->id }}')" class="btn btn-success btn-sm fs-7">
                                    <i class="ki-outline ki-check-circle fs-4 me-1"></i> Selesaikan Pekerjaan
                                </button>
                            @elseif($selectedWorkOrder->status === 'completed')
                                <button type="button" wire:click="closeWorkOrder('{{ $selectedWorkOrder->id }}')" class="btn btn-dark btn-sm fs-7">
                                    <i class="ki-outline ki-verify fs-4 me-1"></i> Verifikasi & Close WO
                                </button>
                            @endif

                            <div class="separator separator-vertical h-25px mx-1 d-none d-sm-block"></div>

                            <button
                                type="button"
                                wire:click="openEditModal('{{ $selectedWorkOrder->id }}')"
                                class="btn btn-sm btn-light fw-semibold fs-8"
                            >
                                <i class="ki-outline ki-pencil fs-6 me-1"></i> Edit WO
                            </button>

                            <button
                                type="button"
                                wire:click="deleteWorkOrder('{{ $selectedWorkOrder->id }}')"
                                wire:confirm="Hapus Work Order ini beserta semua data terkait?"
                                class="btn btn-sm btn-light-danger fw-semibold fs-8"
                            >
                                <i class="ki-outline ki-trash fs-6 me-1"></i> Hapus
                            </button>
                        </div>

                        <button type="button" wire:click="$set('showDetailModal', false)" class="btn btn-light fs-7">
                            <i class="ki-outline ki-cross fs-6 me-1"></i> Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!--end::Modal Detail WO-->

</div>
