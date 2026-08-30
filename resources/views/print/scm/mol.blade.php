@extends('layouts.print', [
    'documentTitle' => 'SURAT PERMINTAAN PENGELUARAN BARANG (MATERIAL ORDER LIST)',
    'documentNumber' => $mol->mol_number,
])

@section('content')
    <div class="doc-header">
        <h2 class="doc-title">SURAT PERMINTAAN & PENGELUARAN BARANG (MATERIAL ORDER)</h2>
        <div class="doc-number">Nomor MOL: <strong>{{ $mol->mol_number }}</strong></div>
        <span class="doc-badge {{ in_array($mol->status, ['issued', 'completed']) ? 'badge-success' : 'badge-warning' }}">
            Status: {{ $mol->status_badge['label'] }}
        </span>
    </div>

    <!-- Meta Information Grid -->
    <div class="meta-box">
        <div class="meta-grid">
            <div>
                <div class="meta-item">
                    <span class="meta-label">No. Work Order (WO)</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value text-primary">{{ $mol->workOrder?->wo_number ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Unit / Equipment</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $mol->workOrder?->equipment?->unit ?? '-' }} - {{ $mol->workOrder?->equipment?->name ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Lokasi Site / Workshop</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $mol->workOrder?->site?->site_name ?? 'Central Workshop' }}</span>
                </div>
            </div>
            <div>
                <div class="meta-item">
                    <span class="meta-label">Tanggal Pengajuan</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $mol->mol_date ? $mol->mol_date->format('d F Y') : $mol->created_at->format('d F Y') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Nama Pemohon (Mekanik)</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $mol->requester->full_name ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Disetujui Oleh (Foreman)</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $mol->approver->full_name ?? 'Plant Foreman' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Material Order Items Table -->
    <table class="table-custom">
        <thead>
            <tr>
                <th class="text-center" style="width: 35px;">No.</th>
                <th style="width: 140px;">Part Number</th>
                <th>Nama Suku Cadang / Material</th>
                <th class="text-center" style="width: 85px;">Qty Minta</th>
                <th class="text-center" style="width: 85px;">Qty Keluar</th>
                <th class="text-center" style="width: 60px;">Satuan</th>
                <th class="text-center" style="width: 120px;">Status Ketersediaan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mol->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $item->part_number }}</td>
                    <td class="fw-semibold">{{ $item->part_name }}</td>
                    <td class="text-center">{{ number_format($item->qty_requested, 0, ',', '.') }}</td>
                    <td class="text-center fw-bold text-success">{{ number_format($item->qty_issued ?? $item->qty_requested, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->part->uom ?? '-' }}</td>
                    <td class="text-center">
                        <span class="fw-semibold {{ $item->status === 'ready_stock' || $item->status === 'issued' ? 'text-success' : 'text-warning' }}">
                            {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada rincian suku cadang.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="notes-box">
        <div class="notes-title">Catatan Penggunaan & Pengeluaran Barang:</div>
        <div>{{ $mol->notes ?: 'Barang yang dikeluarkan hanya untuk keperluan perbaikan unit sesuai nomor Work Order yang tertera.' }}</div>
    </div>

    <!-- Signature Matrix -->
    <div class="signature-section">
        <div class="signature-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="signature-box">
                <div class="signature-role">Pemohon (Mekanik),</div>
                <div class="signature-date">Tgl: {{ $mol->created_at->format('d/m/Y') }}</div>
                <div class="signature-name">{{ $mol->requester->full_name ?? 'Mekanik Unit' }}</div>
                <div class="signature-title">Plant Mechanic</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Disetujui (Foreman),</div>
                <div class="signature-date">Tgl: {{ $mol->created_at->format('d/m/Y') }}</div>
                <div class="signature-name">{{ $mol->approver->full_name ?? 'Plant Foreman' }}</div>
                <div class="signature-title">Foreman / Supervisor</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Petugas Gudang (Storeman),</div>
                <div class="signature-date">Tgl: {{ $mol->created_at->format('d/m/Y') }}</div>
                <div class="signature-name">Storeman Gudang</div>
                <div class="signature-title">Warehouse Issuer</div>
            </div>
        </div>
    </div>
@endsection
