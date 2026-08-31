<div>
    <!--begin::Page Header-->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-6 gap-3">
        <div>
            <h1 class="fs-2x fw-bolder text-gray-900 mb-1">
                <i class="ki-outline ki-setting-3 fs-1 text-primary me-2"></i> PM Service Types - Master Data
            </h1>
            <span class="fs-7 text-muted fw-semibold">
                Kelola tipe service, daftar pekerjaan (Task), dan sparepart untuk setiap interval PM.
            </span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-primary fw-bold" wire:click="openServiceTypeModal()">
                <i class="ki-outline ki-plus fs-4 me-1"></i> Tambah Service Type
            </button>
        </div>
    </div>
    <!--end::Page Header-->

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
    <!--end::Success Message-->

    <!--begin::Metrics Row-->
    <div class="row g-4 mb-6">
        <div class="col-xl-3 col-md-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">Total Service Type</span>
                        <i class="ki-outline ki-setting-3 fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $totalTypes }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background: linear-gradient(135deg, #ff8f00 0%, #e65100 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">Tipe HM</span>
                        <span class="badge text-white" style="background:rgba(255,255,255,0.2);">HM</span>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $hmTypes }}</div>
                    <div class="text-white opacity-75 fs-9">Interval: 250 / 500 / 1000 / 2000 Jam</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background: linear-gradient(135deg, #0288d1 0%, #01579b 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">Tipe KM</span>
                        <span class="badge text-white" style="background:rgba(255,255,255,0.2);">KM</span>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $kmTypes }}</div>
                    <div class="text-white opacity-75 fs-9">Interval: 5000 / 10000 / 20000 / 40000 KM</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-flush shadow-xs border-0 h-100" style="background: linear-gradient(135deg, #388e3c 0%, #1b5e20 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white opacity-75 fw-semibold fs-9 text-uppercase">Total Tasks</span>
                        <i class="ki-outline ki-clipboard fs-3 text-white opacity-50"></i>
                    </div>
                    <div class="fs-2hx fw-bolder text-white">{{ $totalTasks }}</div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Metrics Row-->

    <!--begin::Filter & Search-->
    <div class="card mb-6">
        <div class="card-header border-0 pt-5">
            <div class="card-toolbar">
                <div class="d-flex align-items-center position-relative my-1 me-3">
                    <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                    <input type="text" wire:model.live.debounce.500ms="search" class="form-control form-control-solid w-250px ps-13" placeholder="Cari nama service type..." />
                </div>
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
                            <th>Nama Service Type</th>
                            <th>Tipe & Interval</th>
                            <th>Jumlah Task</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($serviceTypes as $st)
                            <tr>
                                <td>
                                    <span class="text-gray-900 fw-bold fs-7">{{ $st->name }}</span>
                                    @if($st->description)
                                        <span class="text-muted fs-9 d-block text-truncate" style="max-width: 300px;" title="{{ $st->description }}">{{ $st->description }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $st->measurement_type_badge['class'] }} fs-9 fw-bold">{{ strtoupper($st->measurement_type) }}</span>
                                    <span class="text-gray-800 fw-semibold fs-8 ms-1">{{ $st->interval_label }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-light-dark fs-9">{{ $st->tasks_count }} Task</span>
                                </td>
                                <td>
                                    <span class="badge {{ $st->status === 'active' ? 'badge-light-success' : 'badge-light-secondary' }} fs-9">
                                        {{ ucfirst($st->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-light-primary me-1" wire:click="openDetailModal('{{ $st->id }}')" title="Detail">
                                        <i class="ki-outline ki-eye fs-4"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light-warning me-1" wire:click="openServiceTypeModal('{{ $st->id }}')" title="Edit">
                                        <i class="ki-outline ki-notepad fs-4"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-light-danger" wire:click="deleteServiceType('{{ $st->id }}')" onclick="return confirm('Hapus service type ini beserta semua task & part-nya?')" title="Hapus">
                                        <i class="ki-outline ki-trash fs-4"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8">
                                    <i class="ki-outline ki-setting-3 fs-3x text-gray-300 mb-3 d-block"></i>
                                    <span class="text-muted fs-8">Belum ada data Service Type.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <span class="text-muted fs-8">Menampilkan {{ $serviceTypes->firstItem() ?? 0 }} - {{ $serviceTypes->lastItem() ?? 0 }} dari {{ $serviceTypes->total() }} data</span>
                {{ $serviceTypes->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!--begin::Service Type Form Modal-->
    @if($showServiceTypeModal)
    <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" wire:click.self="$set('showServiceTypeModal', false)">
        <div class="modal-dialog modal-dialog-centered mw-600px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">{{ $selectedServiceTypeId ? 'Edit' : 'Tambah' }} Service Type</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" wire:click="$set('showServiceTypeModal', false)">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body py-10 px-lg-17">
                    <form wire:submit.prevent="saveServiceType">
                        <div class="mb-5">
                            <label class="required fs-6 fw-semibold mb-2">Nama Service Type</label>
                            <input type="text" wire:model="st_name" class="form-control form-control-solid" placeholder="Contoh: 250 Hour Service" required />
                            @error('st_name') <span class="text-danger fs-7">{{ $message }}</span> @enderror
                        </div>

                        <div class="row mb-5">
                            <div class="col-md-6">
                                <label class="required fs-6 fw-semibold mb-2">Tipe Pengukuran</label>
                                <select wire:model="st_measurement_type" class="form-select form-select-solid" required>
                                    <option value="hm">Hour Meter (HM)</option>
                                    <option value="km">Kilometer (KM)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="required fs-6 fw-semibold mb-2">Interval Value</label>
                                <select wire:model="st_interval_value" class="form-select form-select-solid" required>
                                    @if($st_measurement_type === 'hm')
                                        <option value="250">250 Jam HM</option>
                                        <option value="500">500 Jam HM</option>
                                        <option value="1000">1000 Jam HM</option>
                                        <option value="2000">2000 Jam HM</option>
                                    @else
                                        <option value="5000">5000 KM</option>
                                        <option value="10000">10000 KM</option>
                                        <option value="20000">20000 KM</option>
                                        <option value="40000">40000 KM</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="fs-6 fw-semibold mb-2">Deskripsi</label>
                            <textarea wire:model="st_description" class="form-control form-control-solid" rows="2" placeholder="Deskripsi singkat service type ini..."></textarea>
                        </div>

                        <div class="mb-8">
                            <label class="required fs-6 fw-semibold mb-2">Status</label>
                            <select wire:model="st_status" class="form-select form-select-solid" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="modal-footer flex-center">
                            <button type="button" wire:click="$set('showServiceTypeModal', false)" class="btn btn-light me-3">Batal</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="saveServiceType">Simpan</span>
                                <span wire:loading wire:target="saveServiceType" class="indicator-progress">Menyimpan... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!--begin::Detail Modal-->
    @if($showDetailModal && $activeServiceType)
    <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" wire:click.self="$set('showDetailModal', false)">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Detail: {{ $activeServiceType->name }}</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" wire:click="$set('showDetailModal', false)">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body py-10 px-lg-17">
                    <div class="d-flex gap-2 mb-6">
                        <span class="badge {{ $activeServiceType->measurement_type_badge['class'] }} fs-8">{{ strtoupper($activeServiceType->measurement_type) }}</span>
                        <span class="badge badge-light-dark fs-8">{{ $activeServiceType->interval_label }}</span>
                        <span class="badge {{ $activeServiceType->status === 'active' ? 'badge-light-success' : 'badge-light-secondary' }} fs-8">{{ ucfirst($activeServiceType->status) }}</span>
                    </div>

                    @if($activeServiceType->tasks->count() > 0)
                        @foreach($activeServiceType->tasks as $task)
                            <div class="card card-flush shadow-sm border border-gray-200 mb-4">
                                <div class="card-header pt-4 pb-2">
                                    <div class="card-title">
                                        <span class="badge badge-light-primary fs-8 fw-bold me-2">Task #{{ $task->task_order }}</span>
                                        <span class="fw-bold text-gray-900 fs-7">{{ $task->task_title }}</span>
                                    </div>
                                    <div class="card-toolbar">
                                        <button type="button" class="btn btn-sm btn-light-warning me-1" wire:click="openTaskModal('{{ $activeServiceType->id }}', '{{ $task->id }}')">
                                            <i class="ki-outline ki-notepad fs-4"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-light-danger" wire:click="deleteTask('{{ $task->id }}')" onclick="return confirm('Hapus task ini?')">
                                            <i class="ki-outline ki-trash fs-4"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body pt-0">
                                    @if($task->notes)
                                        <p class="text-muted fs-8 mb-3">{{ $task->notes }}</p>
                                    @endif
                                    @if($task->parts->count() > 0)
                                        <table class="table table-row-dashed align-middle gs-0 gy-2 fs-8">
                                            <thead>
                                                <tr class="fw-bold text-muted text-uppercase">
                                                    <th>Part Number</th>
                                                    <th>Nama Part</th>
                                                    <th>Qty</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($task->parts as $part)
                                                    <tr>
                                                        <td><span class="fw-bold text-gray-900">{{ $part->part_number }}</span></td>
                                                        <td>{{ $part->part_name }} <span class="badge {{ $part->action_type_badge['class'] }} fs-9 ms-1">{{ $part->action_type_badge['label'] }}</span></td>
                                                        <td>{{ $part->quantity }} {{ $part->unit }}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-light-warning me-1" wire:click="openPartModal('{{ $task->id }}', '{{ $part->id }}')">
                                                                <i class="ki-outline ki-notepad fs-5"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-light-danger" wire:click="deletePart('{{ $part->id }}')" onclick="return confirm('Hapus part ini?')">
                                                                <i class="ki-outline ki-trash fs-5"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <p class="text-muted fs-8">Belum ada part terdaftar.</p>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-light-success" wire:click="openPartModal('{{ $task->id }}')">
                                        <i class="ki-outline ki-plus fs-4 me-1"></i> Tambah Part
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-6">
                            <i class="ki-outline ki-clipboard fs-3x text-gray-300 mb-3 d-block"></i>
                            <span class="text-muted fs-8">Belum ada task untuk service type ini.</span>
                        </div>
                    @endif

                    <button type="button" class="btn btn-sm btn-light-primary" wire:click="openTaskModal('{{ $activeServiceType->id }}')">
                        <i class="ki-outline ki-plus fs-4 me-1"></i> Tambah Task
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!--begin::Task Form Modal-->
    @if($showTaskModal)
    <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" wire:click.self="$set('showTaskModal', false)">
        <div class="modal-dialog modal-dialog-centered mw-500px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">{{ $selectedTaskId ? 'Edit' : 'Tambah' }} Task</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" wire:click="$set('showTaskModal', false)">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body py-10 px-lg-17">
                    <form wire:submit.prevent="saveTask">
                        <div class="mb-5">
                            <label class="required fs-6 fw-semibold mb-2">Judul Task / Pekerjaan</label>
                            <input type="text" wire:model="task_title" class="form-control form-control-solid" placeholder="Contoh: Ganti Oli Mesin" required />
                            @error('task_title') <span class="text-danger fs-7">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-5">
                            <label class="required fs-6 fw-semibold mb-2">Urutan Task</label>
                            <input type="number" wire:model="task_order" class="form-control form-control-solid" min="1" required />
                        </div>
                        <div class="mb-8">
                            <label class="fs-6 fw-semibold mb-2">Catatan</label>
                            <textarea wire:model="task_notes" class="form-control form-control-solid" rows="2"></textarea>
                        </div>
                        <div class="modal-footer flex-center">
                            <button type="button" wire:click="$set('showTaskModal', false)" class="btn btn-light me-3">Batal</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!--begin::Part Form Modal-->
    @if($showPartModal)
    <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);" wire:click.self="$set('showPartModal', false)">
        <div class="modal-dialog modal-dialog-centered mw-600px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">{{ $selectedPartId ? 'Edit' : 'Tambah' }} Part</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" wire:click="$set('showPartModal', false)">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body py-10 px-lg-17">
                    <form wire:submit.prevent="savePart">
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <label class="required fs-6 fw-semibold mb-2">Part Number</label>
                                <input type="text" wire:model="part_number" class="form-control form-control-solid" required />
                            </div>
                            <div class="col-md-6">
                                <label class="required fs-6 fw-semibold mb-2">Nama Part</label>
                                <input type="text" wire:model="part_name" class="form-control form-control-solid" required />
                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col-md-4">
                                <label class="required fs-6 fw-semibold mb-2">Quantity</label>
                                <input type="number" wire:model="part_quantity" class="form-control form-control-solid" min="0.01" step="0.01" required />
                            </div>
                            <div class="col-md-4">
                                <label class="required fs-6 fw-semibold mb-2">Satuan</label>
                                <select wire:model="part_unit" class="form-select form-select-solid" required>
                                    <option value="Pcs">Pcs</option>
                                    <option value="Set">Set</option>
                                    <option value="Liter">Liter</option>
                                    <option value="Box">Box</option>
                                    <option value="Meter">Meter</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="required fs-6 fw-semibold mb-2">Aksi</label>
                                <select wire:model="part_action_type" class="form-select form-select-solid" required>
                                    <option value="replace">Ganti Baru</option>
                                    <option value="check">Cek/Kondisi</option>
                                    <option value="top_up">Tambah/Tambah</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-8">
                            <label class="fs-6 fw-semibold mb-2">Remarks</label>
                            <input type="text" wire:model="part_remarks" class="form-control form-control-solid" />
                        </div>
                        <div class="modal-footer flex-center">
                            <button type="button" wire:click="$set('showPartModal', false)" class="btn btn-light me-3">Batal</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
