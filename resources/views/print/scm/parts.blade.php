@extends('layouts.print', [
    'documentTitle' => 'MASTER PARTS CATALOG (DAFTAR MASTER SUKU CADANG)',
    'documentNumber' => 'PARTS-' . now()->format('ymd'),
])

@section('content')
    <div class="doc-header">
        <h2 class="doc-title">MASTER PARTS CATALOG (DAFTAR MASTER SUKU CADANG)</h2>
        <div class="doc-number">Tanggal Cetak: <strong>{{ now()->format('d F Y') }}</strong></div>
        <span class="doc-badge badge-primary">Total: {{ $parts->count() }} item</span>
    </div>

    <!-- Parts Table -->
    <table class="table-custom">
        <thead>
            <tr>
                <th class="text-center" style="width: 35px;">No.</th>
                <th style="width: 130px;">Part Number</th>
                <th>Nama Suku Cadang</th>
                <th class="text-center" style="width: 70px;">UoM</th>
                <th class="text-end" style="width: 100px;">Standard Cost (Rp)</th>
                <th class="text-center" style="width: 80px;">Stock On Hand</th>
                <th class="text-center" style="width: 70px;">Min Stock</th>
                <th class="text-center" style="width: 70px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($parts as $index => $part)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $part->part_number }}</td>
                    <td class="fw-semibold">{{ $part->name }}</td>
                    <td class="text-center">{{ $part->uom }}</td>
                    <td class="text-end">Rp {{ number_format($part->standard_cost ?? 0, 0, ',', '.') }}</td>
                    <td class="text-center fw-bold {{ $part->stock_on_hand <= ($part->min_stock ?? 0) ? 'text-danger' : 'text-success' }}">
                        {{ $part->stock_on_hand }}
                    </td>
                    <td class="text-center">{{ $part->min_stock ?? '-' }}</td>
                    <td class="text-center">
                        <span class="fw-semibold {{ $part->is_active ? 'text-success' : 'text-danger' }}">
                            {{ $part->is_active ? 'Aktif' : 'Non-Aktif' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data suku cadang.</td>
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
                <div class="signature-name">Staff Procurement</div>
                <div class="signature-title">Procurement Officer</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Diperiksa Oleh,</div>
                <div class="signature-date">Tgl: __ / __ / 202_</div>
                <div class="signature-name">Warehouse Supervisor</div>
                <div class="signature-title">Kepala Gudang</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Disetujui Oleh,</div>
                <div class="signature-date">Tgl: __ / __ / 202_</div>
                <div class="signature-name">Operations Manager</div>
                <div class="signature-title">Operations Manager</div>
            </div>
        </div>
    </div>
@endsection
