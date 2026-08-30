<div>
    @section('title', 'HM Update')

    <div class="d-flex flex-column flex-column-fluid">
        <!-- Toolbar -->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        HM Update & History
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">PLANT</li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-muted">Maintenance</li>
                        <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                        <li class="breadcrumb-item text-dark">HM Update</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <button type="button" class="btn btn-sm btn-light-primary fw-bold" wire:click="$set('showUploadModal', true)">
                        <i class="ki-outline ki-file-up fs-2"></i> Upload Excel
                    </button>
                    <button type="button" class="btn btn-sm btn-primary fw-bold" wire:click="$set('showManualModal', true)">
                        <i class="ki-outline ki-plus fs-2"></i> Input Manual
                    </button>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-fluid">
                
                @if (session()->has('success'))
                    <div class="alert alert-success d-flex align-items-center p-5 mb-5">
                        <i class="ki-outline ki-check-circle fs-2hx text-success me-4"></i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-success">Berhasil</h4>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Filter & Search -->
                <div class="card mb-5 mb-xl-8">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Riwayat Hour Meter (HM)</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Data histori HM aktual maupun hasil perhitungan (interpolasi).</span>
                        </h3>
                        <div class="card-toolbar">
                            <div class="d-flex align-items-center position-relative my-1 me-3">
                                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                                <input type="text" wire:model.live.debounce.500ms="searchUnit" class="form-control form-control-solid w-200px ps-13" placeholder="Cari Kode Unit" />
                            </div>
                            <div class="d-flex align-items-center position-relative my-1">
                                <i class="ki-outline ki-calendar fs-3 position-absolute ms-5"></i>
                                <input type="date" wire:model.live="searchDate" class="form-control form-control-solid w-150px ps-13" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body py-3">
                        <div class="table-responsive">
                            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4 min-w-100px rounded-start">Tanggal</th>
                                        <th class="min-w-125px">Unit</th>
                                        <th class="min-w-100px text-end">HM Value</th>
                                        <th class="min-w-100px text-center">Status</th>
                                        <th class="min-w-100px text-center">Sumber</th>
                                        <th class="min-w-150px rounded-end">Updated By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($logs as $log)
                                        <tr class="{{ $log->is_interpolated ? 'bg-light-warning opacity-75' : '' }}">
                                            <td class="ps-4">
                                                <span class="text-dark fw-bold d-block fs-7">{{ \Carbon\Carbon::parse($log->date)->translatedFormat('d M Y') }}</span>
                                            </td>
                                            <td>
                                                <span class="text-dark fw-bold text-hover-primary d-block fs-6">{{ $log->equipment->unit ?? '-' }}</span>
                                                <span class="text-muted fw-semibold text-muted d-block fs-7">{{ $log->equipment->no ?? '-' }}</span>
                                            </td>
                                            <td class="text-end">
                                                <span class="text-dark fw-bold fs-5">{{ number_format($log->hm_value, 0, ',', '.') }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($log->is_interpolated)
                                                    <span class="badge badge-light-warning">Interpolated (Auto)</span>
                                                @else
                                                    <span class="badge badge-light-success">Actual</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-outline badge-primary">{{ strtoupper($log->source ?? 'UNKNOWN') }}</span>
                                            </td>
                                            <td>
                                                <span class="text-dark fw-semibold d-block fs-7">{{ $log->updater->full_name ?? 'System' }}</span>
                                                <span class="text-muted fw-semibold d-block fs-8">{{ $log->updated_at->translatedFormat('d M Y H:i') }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-10">
                                                Belum ada histori HM ditemukan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-5">
                            <div>
                                Menampilkan {{ $logs->firstItem() ?? 0 }} s/d {{ $logs->lastItem() ?? 0 }} dari total {{ $logs->total() }} data
                            </div>
                            <div>
                                {{ $logs->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @if($showManualModal)
    <!-- Modal Input Manual -->
    <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered mw-500px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Input HM Manual</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" wire:click="$set('showManualModal', false)">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body py-10 px-lg-17">
                    <form wire:submit.prevent="saveManual">
                        <div class="mb-5">
                            <label class="required fs-6 fw-semibold mb-2">Unit Equipment</label>
                            <select wire:model="equipment_id" class="form-select form-select-solid" required>
                                <option value="">-- Pilih Unit --</option>
                                @foreach($equipments as $eq)
                                    <option value="{{ $eq->id }}">{{ $eq->unit }} ({{ $eq->no }})</option>
                                @endforeach
                            </select>
                            @error('equipment_id') <span class="text-danger fs-7">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-5">
                            <label class="required fs-6 fw-semibold mb-2">Tanggal (DD MMM YYYY)</label>
                            <!-- Visual input using date picker, but backend reads standard date -->
                            <input type="date" wire:model="date" class="form-control form-control-solid" required />
                            @error('date') <span class="text-danger fs-7">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-5">
                            <label class="required fs-6 fw-semibold mb-2">Nilai HM Aktual</label>
                            <input type="number" wire:model="hm_value" class="form-control form-control-solid" placeholder="Contoh: 12500" required />
                            @error('hm_value') <span class="text-danger fs-7">{{ $message }}</span> @enderror
                        </div>

                        <div class="modal-footer flex-center">
                            <button type="button" wire:click="$set('showManualModal', false)" class="btn btn-light me-3">Batal</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="saveManual">Simpan HM</span>
                                <span wire:loading wire:target="saveManual" class="indicator-progress">
                                    Menyimpan... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($showUploadModal)
    <!-- Modal Upload Excel -->
    <div class="modal fade show" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered mw-600px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Upload HM Massal (Excel)</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" wire:click="$set('showUploadModal', false)">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body py-10 px-lg-17">
                    
                    <div class="alert alert-primary d-flex align-items-center p-4 mb-8">
                        <i class="ki-outline ki-information fs-2hx text-primary me-4"></i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-primary">Cara Mengisi Template</h4>
                            <span>1. Unduh template Excel di bawah ini.<br>
                                  2. Isi kolom <strong>Unit Code</strong> sesuai dengan master data (misal: EX-001).<br>
                                  3. Kolom <strong>Tanggal</strong> wajib berformat Date di Excel atau <strong>DD MMM YYYY</strong> (contoh: 01 Jan 2026).<br>
                                  4. Kolom <strong>HM</strong> berisi angka bulat tanpa koma.<br>
                                  5. Simpan dan unggah kembali.
                            </span>
                        </div>
                    </div>

                    <div class="text-center mb-8">
                        <button type="button" wire:click="downloadTemplate" class="btn btn-sm btn-success">
                            <i class="ki-outline ki-file-down fs-2"></i> Unduh Template Excel
                        </button>
                    </div>

                    <form wire:submit.prevent="uploadExcel">
                        <div class="mb-5">
                            <label class="required fs-6 fw-semibold mb-2">Pilih File (XLSX, XLS, CSV)</label>
                            <input type="file" wire:model="excel_file" class="form-control form-control-solid" accept=".xlsx, .xls, .csv" required />
                            @error('excel_file') <span class="text-danger fs-7">{{ $message }}</span> @enderror
                        </div>

                        <div class="modal-footer flex-center">
                            <button type="button" wire:click="$set('showUploadModal', false)" class="btn btn-light me-3">Batal</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" {{ !$excel_file ? 'disabled' : '' }}>
                                <span wire:loading.remove wire:target="uploadExcel">Proses Upload</span>
                                <span wire:loading wire:target="uploadExcel" class="indicator-progress">
                                    Memproses... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    window.addEventListener('close-modal', event => {
        $('#' + event.detail.id).modal('hide');
        $('.modal-backdrop').remove();
    });
</script>
@endpush
