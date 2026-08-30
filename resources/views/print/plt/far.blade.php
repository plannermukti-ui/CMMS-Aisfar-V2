@extends('layouts.print', [
    'documentTitle' => 'FAILURE ANALYSIS REPORT (FAR)',
    'documentNumber' => $far->far_number,
])

@section('content')
    <div class="doc-header">
        <h2 class="doc-title">FAILURE ANALYSIS REPORT (FAR)</h2>
        <div class="doc-number">Nomor FAR: <strong>{{ $far->far_number }}</strong></div>
        <span class="doc-badge {{ $far->status === 'closed' ? 'badge-success' : 'badge-warning' }}">
            Status: {{ $far->status_badge['label'] }}
        </span>
    </div>

    <!-- Meta Information Grid -->
    <div class="meta-box">
        <div class="meta-grid">
            <div>
                <div class="meta-item">
                    <span class="meta-label">Tanggal Insiden</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $far->incident_date->format('d F Y') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Unit / Equipment</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value text-primary">{{ $far->equipment->unit ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Komponen</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $far->component->name ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Work Order Terkait</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $far->workOrder->wo_number ?? '-' }}</span>
                </div>
            </div>
            <div>
                <div class="meta-item">
                    <span class="meta-label">HM Unit Saat Failure</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ number_format($far->unit_hm_at_failure, 0, ',', '.') }} HM</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">HM Komponen Saat Failure</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ number_format($far->component_hm_at_failure, 0, ',', '.') }} jam</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Jenis Failure</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $far->failure_type_badge['label'] ?? ucfirst(str_replace('_', ' ', $far->failure_type)) }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Investigator</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $far->investigator->full_name ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Failure Title -->
    <div class="notes-box">
        <div class="notes-title">Judul Failure / Failure Title:</div>
        <div class="fw-semibold text-danger">{{ $far->failure_title }}</div>
    </div>

    <!-- Problem Statement -->
    <div class="notes-box">
        <div class="notes-title">Pernyataan Masalah / Problem Statement:</div>
        <div>{{ $far->problem_statement }}</div>
    </div>

    <!-- Failure Symptoms -->
    <div class="notes-box">
        <div class="notes-title">Gejala Failure / Failure Symptoms:</div>
        <div>{{ $far->failure_symptoms }}</div>
    </div>

    <!-- Root Cause Analysis - 5 Why -->
    @if($far->root_cause_5why && count($far->root_cause_5why))
        <table class="table-custom" style="margin-top: 12px;">
            <thead>
                <tr>
                    <th style="width: 60px;" class="text-center">Level</th>
                    <th>Analisis 5 Why</th>
                </tr>
            </thead>
            <tbody>
                @foreach($far->root_cause_5why as $key => $value)
                    <tr>
                        <td class="text-center fw-bold">{{ strtoupper(str_replace('why', '', $key)) }}</td>
                        <td>{{ $value }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Fishbone Factors -->
    @if($far->fishbone_factors && count($far->fishbone_factors))
        <table class="table-custom" style="margin-top: 12px;">
            <thead>
                <tr>
                    <th style="width: 140px;">Faktor</th>
                    <th>Analisis Fishbone (Ishikawa)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($far->fishbone_factors as $factor => $detail)
                    <tr>
                        <td class="fw-semibold">{{ ucfirst($factor) }}</td>
                        <td>{{ $detail }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Root Cause Summary -->
    <div class="notes-box">
        <div class="notes-title">Ringkasan Akar Masalah / Root Cause Summary:</div>
        <div class="fw-semibold">{{ $far->root_cause_summary }}</div>
    </div>

    <div class="notes-box">
        <div class="notes-title">Penyebab Langsung / Direct Cause:</div>
        <div>{{ $far->direct_cause }}</div>
    </div>

    <!-- Corrective & Preventive Actions -->
    <div class="meta-box" style="margin-top: 12px;">
        <div class="meta-grid">
            <div>
                <div class="meta-item" style="flex-direction: column;">
                    <span class="meta-label fw-bold" style="width: auto;">Tindakan Korektif (Corrective Actions):</span>
                    <span class="meta-value" style="margin-top: 4px;">{{ $far->corrective_actions }}</span>
                </div>
            </div>
            <div>
                <div class="meta-item" style="flex-direction: column;">
                    <span class="meta-label fw-bold" style="width: auto;">Tindakan Preventif (Preventive Actions):</span>
                    <span class="meta-value" style="margin-top: 4px;">{{ $far->preventive_actions }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Impact Estimate -->
    @if($far->cost_impact_estimate || $far->downtime_hours_estimate)
        <table class="table-custom" style="margin-top: 12px;">
            <thead>
                <tr>
                    <th>Estimasi Dampak / Impact Estimate</th>
                    <th class="text-end" style="width: 180px;">Nilai</th>
                </tr>
            </thead>
            <tbody>
                @if($far->cost_impact_estimate)
                    <tr>
                        <td class="fw-semibold">Estimasi Biaya (Cost Impact)</td>
                        <td class="text-end fw-bold">Rp {{ number_format($far->cost_impact_estimate, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if($far->downtime_hours_estimate)
                    <tr>
                        <td class="fw-semibold">Estimasi Downtime</td>
                        <td class="text-end fw-bold">{{ number_format($far->downtime_hours_estimate, 1) }} jam</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

    <!-- Signature Matrix -->
    <div class="signature-section">
        <div class="signature-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="signature-box">
                <div class="signature-role">Investigator,</div>
                <div class="signature-date">Tgl: {{ $far->incident_date->format('d/m/Y') }}</div>
                <div class="signature-name">{{ $far->investigator->full_name ?? 'Investigator' }}</div>
                <div class="signature-title">Maintenance Planner / Engineer</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Ditinjau Oleh,</div>
                <div class="signature-date">Tgl: __ / __ / 202_</div>
                <div class="signature-name">Maintenance Supervisor</div>
                <div class="signature-title">Maintenance Foreman</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Disetujui Oleh,</div>
                <div class="signature-date">Tgl: __ / __ / 202_</div>
                <div class="signature-name">Plant Manager</div>
                <div class="signature-title">Plant Manager</div>
            </div>
        </div>
    </div>
@endsection
