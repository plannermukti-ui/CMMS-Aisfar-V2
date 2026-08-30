@extends('layouts.print', [
    'documentTitle' => 'SURAT PENGAJUAN PEMBELIAN (PURCHASE REQUISITION)',
    'documentNumber' => $pr->pr_number,
])

@section('content')
    <div class="doc-header">
        <h2 class="doc-title">SURAT PENGAJUAN PEMBELIAN (PURCHASE REQUISITION)</h2>
        <div class="doc-number">Nomor PR: <strong>{{ $pr->pr_number }}</strong></div>
        <span class="doc-badge {{ in_array($pr->status, ['approved', 'po_created']) ? 'badge-success' : 'badge-primary' }}">
            Status: {{ $pr->status_badge['label'] }}
        </span>
    </div>

    <!-- Meta Information Grid -->
    <div class="meta-box">
        <div class="meta-grid">
            <div>
                <div class="meta-item">
                    <span class="meta-label">Nama Pemohon</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $pr->requester->full_name ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Lokasi Site / Tambang</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value text-primary">{{ $pr->site?->site_name ?? 'Central Site' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">No. Referensi MOL</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $pr->materialOrder->mol_number ?? '-' }}</span>
                </div>
            </div>
            <div>
                <div class="meta-item">
                    <span class="meta-label">Tanggal Pengajuan</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $pr->pr_date ? $pr->pr_date->format('d F Y') : $pr->created_at->format('d F Y') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Tingkat Prioritas</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value {{ in_array($pr->priority, ['emergency', 'high']) ? 'text-danger fw-bold' : '' }}">
                        {{ strtoupper($pr->priority ?? 'NORMAL') }}
                    </span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Dibutuhkan Tanggal</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $pr->required_date ? $pr->required_date->format('d F Y') : 'Segera' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- PR Items Table -->
    <table class="table-custom">
        <thead>
            <tr>
                <th class="text-center" style="width: 35px;">No.</th>
                <th style="width: 140px;">Part Number</th>
                <th>Deskripsi Barang / Spesifikasi Teknis</th>
                <th class="text-center" style="width: 60px;">Qty</th>
                <th class="text-center" style="width: 50px;">UoM</th>
                <th class="text-end" style="width: 110px;">Est. Harga (Rp)</th>
                <th class="text-end" style="width: 120px;">Est. Subtotal (Rp)</th>
                <th style="width: 130px;">Kebutuhan / Alasan</th>
            </tr>
        </thead>
        <tbody>
            @php $totalEst = 0; @endphp
            @forelse($pr->items as $index => $item)
                @php
                    $sub = (float)$item->quantity * (float)$item->estimated_unit_price;
                    $totalEst += $sub;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $item->part_number }}</td>
                    <td class="fw-semibold">{{ $item->part_name }}</td>
                    <td class="text-center fw-bold">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->uom }}</td>
                    <td class="text-end">Rp {{ number_format($item->estimated_unit_price, 0, ',', '.') }}</td>
                    <td class="text-end fw-semibold">Rp {{ number_format($sub, 0, ',', '.') }}</td>
                    <td style="font-size: 9.5px;" class="text-muted">{{ $item->notes ?: 'Kebutuhan sparepart unit' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada rincian item pengajuan pembelian.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f1f5f9;">
                <td colspan="6" class="text-end fw-bold">TOTAL ESTIMASI ANGGARAN PEMBELIAN:</td>
                <td class="text-end fw-bold text-primary">Rp {{ number_format($totalEst, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="notes-box">
        <div class="notes-title">Justifikasi Pembelian & Catatan Tambahan:</div>
        <div>{{ $pr->remarks ?: 'Pengadaan suku cadang ini diajukan untuk mendukung kesiapan operasional alat berat dan kelancaran kegiatan maintenance unit di site.' }}</div>
    </div>

    <!-- Signature Matrix -->
    <div class="signature-section">
        <div class="signature-grid">
            <div class="signature-box">
                <div class="signature-role">Diajukan Oleh,</div>
                <div class="signature-date">Tgl: {{ $pr->created_at->format('d/m/Y') }}</div>
                <div class="signature-name">{{ $pr->requester->full_name ?? 'Planner / Staff' }}</div>
                <div class="signature-title">Requester</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Diperiksa Oleh,</div>
                <div class="signature-date">Tgl: {{ $pr->created_at->format('d/m/Y') }}</div>
                <div class="signature-name">Plant Section Head</div>
                <div class="signature-title">Superintendent Maintenance</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Disetujui Oleh,</div>
                <div class="signature-date">Tgl: {{ $pr->created_at->format('d/m/Y') }}</div>
                <div class="signature-name">{{ $pr->approver->full_name ?? 'Project Manager' }}</div>
                <div class="signature-title">Site / Project Manager</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Diterima Purchasing,</div>
                <div class="signature-date">Tgl: __ / __ / 202_</div>
                <div class="signature-name">Procurement HO</div>
                <div class="signature-title">Buyer / Purchasing Head</div>
            </div>
        </div>
    </div>
@endsection
