@extends('layouts.print', [
    'documentTitle' => 'OUTSIDE REPAIR ORDER (OSR)',
    'documentNumber' => $osr->osr_number,
])

@section('content')
    <div class="doc-header">
        <h2 class="doc-title">OUTSIDE REPAIR ORDER (OSR)</h2>
        <div class="doc-number">Nomor OSR: <strong>{{ $osr->osr_number }}</strong></div>
        <span class="doc-badge {{ in_array($osr->status, ['received_at_site', 'closed']) ? 'badge-success' : 'badge-primary' }}">
            Status: {{ $osr->status_badge['label'] }}
        </span>
    </div>

    <!-- Meta Information Grid -->
    <div class="meta-box">
        <div class="meta-grid">
            <div>
                <div class="meta-item">
                    <span class="meta-label">Tanggal Order</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $osr->order_date->format('d F Y') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Unit / Equipment</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value text-primary">{{ $osr->equipment->unit ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Komponen</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $osr->component->name ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Work Order Terkait</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $osr->workOrder->wo_number ?? '-' }}</span>
                </div>
            </div>
            <div>
                <div class="meta-item">
                    <span class="meta-label">Vendor / Bengkel Luar</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value text-primary">{{ $osr->vendor->name ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Deskripsi Item</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $osr->item_description }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Tanggal Dispatch</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $osr->dispatch_date ? $osr->dispatch_date->format('d F Y') : '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Estimasi Selesai</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $osr->estimated_completion_date ? $osr->estimated_completion_date->format('d F Y') : '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Scope of Work -->
    <div class="notes-box">
        <div class="notes-title">Cakupan Pekerjaan / Scope of Work:</div>
        <div>{{ $osr->scope_of_work }}</div>
    </div>

    <!-- Reason for Outside Repair -->
    <div class="notes-box">
        <div class="notes-title">Alasan Pekerjaan Diluar / Reason for Outside Repair:</div>
        <div>{{ $osr->reason_for_outside }}</div>
    </div>

    <!-- Cost & Warranty Details -->
    <table class="table-custom" style="margin-top: 12px;">
        <thead>
            <tr>
                <th>Detail Biaya & Garansi</th>
                <th class="text-end" style="width: 200px;">Nilai</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="fw-semibold">Estimasi Biaya</td>
                <td class="text-end fw-bold">Rp {{ number_format($osr->estimated_cost, 0, ',', '.') }}</td>
            </tr>
            @if($osr->actual_cost)
                <tr>
                    <td class="fw-semibold">Biaya Aktual</td>
                    <td class="text-end fw-bold text-primary">Rp {{ number_format($osr->actual_cost, 0, ',', '.') }}</td>
                </tr>
            @endif
            @if($osr->warranty_period_months)
                <tr>
                    <td class="fw-semibold">Masa Garansi (Bulan)</td>
                    <td class="text-end fw-bold">{{ $osr->warranty_period_months }} bulan</td>
                </tr>
            @endif
            @if($osr->warranty_period_hours)
                <tr>
                    <td class="fw-semibold">Masa Garansi (Jam)</td>
                    <td class="text-end fw-bold">{{ number_format($osr->warranty_period_hours, 0, ',', '.') }} jam</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- QC Result -->
    <div class="notes-box" style="margin-top: 12px;">
        <div class="notes-title">Hasil Quality Control / QC Result:</div>
        <div class="fw-semibold {{ $osr->qc_passed ? 'text-success' : 'text-danger' }}">
            {{ $osr->qc_passed ? 'PASS (Lulus Uji)' : 'FAIL (Tidak Lulus Uji)' }}
        </div>
        @if($osr->qc_notes)
            <div style="margin-top: 4px; font-size: 10.5px; color: #475569;">Catatan QC: {{ $osr->qc_notes }}</div>
        @endif
    </div>

    <!-- Signature Matrix -->
    <div class="signature-section">
        <div class="signature-grid">
            <div class="signature-box">
                <div class="signature-role">Dibuat Oleh,</div>
                <div class="signature-date">Tgl: {{ $osr->order_date->format('d/m/Y') }}</div>
                <div class="signature-name">{{ $osr->creator->full_name ?? 'Planner' }}</div>
                <div class="signature-title">Maintenance Planner</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Dikirim Oleh,</div>
                <div class="signature-date">Tgl: {{ $osr->dispatch_date ? $osr->dispatch_date->format('d/m/Y') : '__ / __ / 202_' }}</div>
                <div class="signature-name">Logistics / Warehouse</div>
                <div class="signature-title">Logistics Officer</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Diterima Oleh (Vendor),</div>
                <div class="signature-date">Tgl: __ / __ / 202_</div>
                <div class="signature-name">{{ $osr->vendor->name ?? 'Vendor' }}</div>
                <div class="signature-title">Tanda Tangan Vendor</div>
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
