@extends('layouts.print', [
    'documentTitle' => 'COMPONENT CONDITION REPORT (CCR)',
    'documentNumber' => $ccr->ccr_number,
])

@section('content')
    <div class="doc-header">
        <h2 class="doc-title">COMPONENT CONDITION REPORT (CCR)</h2>
        <div class="doc-number">Nomor CCR: <strong>{{ $ccr->ccr_number }}</strong></div>
        <span class="doc-badge {{ $ccr->status === 'approved' ? 'badge-success' : 'badge-warning' }}">
            Status: {{ $ccr->status_badge['label'] }}
        </span>
    </div>

    <!-- Meta Information Grid -->
    <div class="meta-box">
        <div class="meta-grid">
            <div>
                <div class="meta-item">
                    <span class="meta-label">Tanggal Inspeksi</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $ccr->ccr_date->format('d F Y') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Unit / Equipment</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value text-primary">{{ $ccr->equipment->unit ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Komponen</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $ccr->component->name ?? $ccr->component_name ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">HM Unit Saat Inspeksi</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ number_format($ccr->current_unit_hm, 0, ',', '.') }} HM</span>
                </div>
            </div>
            <div>
                <div class="meta-item">
                    <span class="meta-label">Jam Komponen (Accumulated)</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ number_format($ccr->component_running_hours, 0, ',', '.') }} jam</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Persentase Keausan</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value fw-bold {{ $ccr->wear_percentage > 75 ? 'text-danger' : ($ccr->wear_percentage > 50 ? 'text-warning' : 'text-success') }}">{{ $ccr->wear_percentage }}%</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Estimasi Sisa Usia</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ number_format($ccr->estimated_remaining_hours, 0, ',', '.') }} jam</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Inspektur</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $ccr->inspector->full_name ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Condition Details Table -->
    <table class="table-custom">
        <thead>
            <tr>
                <th style="width: 40px;" class="text-center">No.</th>
                <th style="width: 220px;">Parameter Pemeriksaan</th>
                <th>Kondisi / Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td class="fw-semibold">Physical Condition (Kondisi Fisik)</td>
                <td>{{ ucfirst(str_replace('_', ' ', $ccr->physical_condition)) }}</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td class="fw-semibold">Leakage Status (Status Kebocoran)</td>
                <td>{{ ucfirst(str_replace('_', ' ', $ccr->leakage_status)) }}</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td class="fw-semibold">Noise & Vibration (Suara & Getaran)</td>
                <td>{{ ucfirst(str_replace('_', ' ', $ccr->noise_vibration_status)) }}</td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td class="fw-semibold">Oil Contamination (Kontaminasi Oli)</td>
                <td>{{ ucfirst(str_replace('_', ' ', $ccr->oil_contamination_status)) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Findings -->
    <div class="notes-box">
        <div class="notes-title">Temuan Inspeksi / Findings Description:</div>
        <div>{{ $ccr->findings_description }}</div>
    </div>

    <!-- Recommendation -->
    <div class="notes-box">
        <div class="notes-title">Rekomendasi Tindak Lanjut / Recommendation:</div>
        <div class="fw-semibold text-primary">{{ $ccr->recommendation_badge['label'] ?? ucfirst(str_replace('_', ' ', $ccr->recommendation)) }}</div>
        @if($ccr->action_taken)
            <div style="margin-top: 6px; font-size: 10.5px; color: #475569;">Tindakan yang sudah diambil: {{ $ccr->action_taken }}</div>
        @endif
    </div>

    @if($ccr->workOrder)
        <div class="notes-box">
            <div class="notes-title">Work Order Terkait:</div>
            <div class="text-primary">{{ $ccr->workOrder->wo_number ?? '-' }}</div>
        </div>
    @endif

    <!-- Signature Matrix -->
    <div class="signature-section">
        <div class="signature-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="signature-box">
                <div class="signature-role">Inspektur / Pemeriksa,</div>
                <div class="signature-date">Tgl: {{ $ccr->ccr_date->format('d/m/Y') }}</div>
                <div class="signature-name">{{ $ccr->inspector->full_name ?? 'Inspector' }}</div>
                <div class="signature-title">Field Inspector / Planner</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Ditinjau Oleh,</div>
                <div class="signature-date">Tgl: __ / __ / 202_</div>
                <div class="signature-name">Planner / Maintenance Supervisor</div>
                <div class="signature-title">Maintenance Planner</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Disetujui Oleh,</div>
                <div class="signature-date">Tgl: __ / __ / 202_</div>
                <div class="signature-name">Plant Manager</div>
                <div class="signature-title">Plant Manager / Maintenance Manager</div>
            </div>
        </div>
    </div>
@endsection
