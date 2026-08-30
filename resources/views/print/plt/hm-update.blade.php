@extends('layouts.print', [
    'documentTitle' => 'HM UPDATE (HOUR METER UPDATE REPORT)',
    'documentNumber' => 'HM-UPD-' . now()->format('ymd'),
])

@section('content')
    <div class="doc-header">
        <h2 class="doc-title">HM UPDATE (HOUR METER UPDATE REPORT)</h2>
        <div class="doc-number">Tanggal Cetak: <strong>{{ now()->format('d F Y') }}</strong></div>
    </div>

    <!-- Equipment HM Summary Table -->
    <table class="table-custom">
        <thead>
            <tr>
                <th class="text-center" style="width: 35px;">No.</th>
                <th style="width: 140px;">Unit / Equipment</th>
                <th>Site / Lokasi</th>
                <th class="text-end" style="width: 100px;">HM Terakhir</th>
                <th>Terakhir Dicatat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($equipments as $index => $equipment)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold text-primary">{{ $equipment->unit }}</td>
                    <td>{{ $equipment->site->site_name ?? '-' }}</td>
                    <td class="text-end fw-bold">{{ $equipment->latestHmLog ? number_format($equipment->latestHmLog->hm_value, 0, ',', '.') . ' HM' : '-' }}</td>
                    <td>{{ $equipment->latestHmLog->creator->full_name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data equipment.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Recent HM Logs -->
    <h3 style="font-size: 12px; font-weight: 700; margin: 16px 0 6px; color: #0f172a;">Riwayat Pencatatan HM Terbaru (Recent HM Logs)</h3>
    <table class="table-custom">
        <thead>
            <tr>
                <th class="text-center" style="width: 35px;">No.</th>
                <th>Tanggal Pencatatan</th>
                <th>Unit / Equipment</th>
                <th class="text-end" style="width: 120px;">Nilai HM</th>
                <th>Sumber Data</th>
                <th>Dicatat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentLogs as $index => $log)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $log->date->format('d/m/Y') }}</td>
                    <td class="fw-bold">{{ $log->equipment->unit ?? '-' }}</td>
                    <td class="text-end fw-bold">{{ number_format($log->hm_value, 0, ',', '.') }}</td>
                    <td class="text-center">{{ ucfirst($log->source ?? 'manual') }}</td>
                    <td>{{ $log->creator->full_name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada riwayat pencatatan HM.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="signature-box">
                <div class="signature-role">Disiapkan Oleh,</div>
                <div class="signature-date">Tgl: {{ now()->format('d/m/Y') }}</div>
                <div class="signature-name">Maintenance Planner</div>
                <div class="signature-title">Maintenance Planner</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Diperiksa Oleh,</div>
                <div class="signature-date">Tgl: __ / __ / 202_</div>
                <div class="signature-name">Maintenance Supervisor</div>
                <div class="signature-title">Foreman / Supervisor</div>
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
