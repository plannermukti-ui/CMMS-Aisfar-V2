@extends('layouts.print', [
    'documentTitle' => 'WORK ORDER (SURAT PERINTAH KERJA)',
    'documentNumber' => $workOrder->wo_number,
])

@section('content')
    <div class="doc-header">
        <h2 class="doc-title">WORK ORDER (SURAT PERINTAH KERJA)</h2>
        <div class="doc-number">Nomor WO: <strong>{{ $workOrder->wo_number }}</strong></div>
        <span class="doc-badge {{ in_array($workOrder->status, ['completed', 'closed']) ? 'badge-success' : ($workOrder->status === 'cancelled' ? 'badge-danger' : 'badge-primary') }}">
            Status: {{ $workOrder->status_badge['label'] }}
        </span>
    </div>

    <!-- Meta Information Grid -->
    <div class="meta-box">
        <div class="meta-grid">
            <div>
                <div class="meta-item">
                    <span class="meta-label">Tanggal WO</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $workOrder->wo_date->format('d F Y') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Unit / Equipment</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value text-primary">{{ $workOrder->equipment->unit ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Site / Lokasi</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $workOrder->site->site_name ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Jenis Pekerjaan</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $workOrder->type_badge['label'] ?? ucfirst($workOrder->wo_type) }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Prioritas</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $workOrder->priority_badge['label'] ?? ucfirst($workOrder->priority) }}</span>
                </div>
            </div>
            <div>
                <div class="meta-item">
                    <span class="meta-label">HM Unit Saat Ini</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $workOrder->current_hm ?? '-' }} HM</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Pemohon</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $workOrder->requester->full_name ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Ditugaskan Ke</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $workOrder->assignedTo->full_name ?? 'Belum Ditugaskan' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Status Unit</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $workOrder->unit_status_badge['label'] ?? ucfirst($workOrder->unit_status ?? 'Breakdown') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Disetujui Oleh</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $workOrder->approvedBy->full_name ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Job Description -->
    <div class="notes-box">
        <div class="notes-title">Judul Pekerjaan / Job Title:</div>
        <div class="fw-semibold">{{ $workOrder->job_title }}</div>
    </div>

    <div class="notes-box">
        <div class="notes-title">Deskripsi Masalah / Problem Description:</div>
        <div>{{ $workOrder->problem_description }}</div>
    </div>

    @if($workOrder->action_taken)
        <div class="notes-box">
            <div class="notes-title">Tindakan Yang Diambil / Action Taken:</div>
            <div>{{ $workOrder->action_taken }}</div>
        </div>
    @endif

    @if($workOrder->root_cause)
        <div class="notes-box">
            <div class="notes-title">Akar Masalah / Root Cause:</div>
            <div>{{ $workOrder->root_cause }}</div>
        </div>
    @endif

    <!-- Tasks Table -->
    @if($workOrder->tasks->count())
        <table class="table-custom" style="margin-top: 12px;">
            <thead>
                <tr>
                    <th class="text-center" style="width: 35px;">No.</th>
                    <th>Deskripsi Task / Job Task Description</th>
                    <th class="text-center" style="width: 120px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($workOrder->tasks as $index => $task)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <span class="fw-semibold">{{ $task->description ?? $task->task_name ?? '-' }}</span>
                            @if($task->subtasks->count())
                                <ul style="margin: 4px 0 0 16px; font-size: 10px; color: #475569;">
                                    @foreach($task->subtasks as $subtask)
                                        <li>{{ $subtask->description ?? $subtask->name ?? '-' }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>
                        <td class="text-center">{{ ucfirst($task->status ?? 'pending') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Signature Matrix -->
    <div class="signature-section">
        <div class="signature-grid">
            <div class="signature-box">
                <div class="signature-role">Dibuat Oleh,</div>
                <div class="signature-date">Tgl: {{ $workOrder->wo_date->format('d/m/Y') }}</div>
                <div class="signature-name">{{ $workOrder->requester->full_name ?? 'Requester' }}</div>
                <div class="signature-title">Pemohon / Requester</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Dikerjakan Oleh,</div>
                <div class="signature-date">Tgl: __ / __ / 202_</div>
                <div class="signature-name">{{ $workOrder->assignedTo->full_name ?? 'Mekanik' }}</div>
                <div class="signature-title">Mechanic / Technician</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Diperiksa Oleh,</div>
                <div class="signature-date">Tgl: __ / __ / 202_</div>
                <div class="signature-name">Foreman / Supervisor</div>
                <div class="signature-title">Plant Foreman</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Disetujui Oleh,</div>
                <div class="signature-date">Tgl: __ / __ / 202_</div>
                <div class="signature-name">{{ $workOrder->approvedBy->full_name ?? 'Plant Manager' }}</div>
                <div class="signature-title">Plant Manager</div>
            </div>
        </div>
    </div>
@endsection
