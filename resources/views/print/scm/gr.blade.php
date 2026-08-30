@extends('layouts.print', [
    'documentTitle' => 'BUKTI PENERIMAAN BARANG GUDANG (GOODS RECEIPT)',
    'documentNumber' => $gr->gr_number,
])

@section('content')
    <div class="doc-header">
        <h2 class="doc-title">BUKTI PENERIMAAN BARANG GUDANG (GOODS RECEIPT)</h2>
        <div class="doc-number">Nomor GR: <strong>{{ $gr->gr_number }}</strong></div>
        <span class="doc-badge {{ $gr->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
            Status: {{ $gr->status_badge['label'] }}
        </span>
    </div>

    <!-- Meta Information Grid -->
    <div class="meta-box">
        <div class="meta-grid">
            <div>
                <div class="meta-item">
                    <span class="meta-label">No. Surat Jalan Vendor/DO</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value text-primary">{{ $gr->delivery_order_number ?: ($gr->deliveryOrder->do_number ?? '-') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">No. PO Referensi</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $gr->purchaseOrder->po_number ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Vendor / Supplier</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $gr->purchaseOrder->vendor->name ?? '-' }}</span>
                </div>
            </div>
            <div>
                <div class="meta-item">
                    <span class="meta-label">Tanggal Penerimaan</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $gr->gr_date->format('d F Y') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Lokasi Gudang / Site</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value text-primary">{{ $gr->site->site_name ?? 'Central Warehouse' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Petugas Penerima</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $gr->receiver->full_name ?? 'Storeman' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Received Items Table -->
    <table class="table-custom">
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">No.</th>
                <th style="width: 150px;">Part Number</th>
                <th>Nama / Deskripsi Suku Cadang</th>
                <th class="text-center" style="width: 110px;">Qty Diterima</th>
                <th class="text-end" style="width: 120px;">Unit Price (Rp)</th>
                <th class="text-end" style="width: 140px;">Total Nilai Masuk</th>
            </tr>
        </thead>
        <tbody>
            @php $totalNilai = 0; @endphp
            @forelse($gr->items as $index => $item)
                @php
                    $sub = (float)$item->qty_received * (float)$item->unit_price;
                    $totalNilai += $sub;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $item->part_number }}</td>
                    <td class="fw-semibold">{{ $item->part_name }}</td>
                    <td class="text-center fw-bold text-success">+{{ number_format($item->qty_received, 0, ',', '.') }} {{ $item->part->uom ?? 'Pcs' }}</td>
                    <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-end fw-semibold">Rp {{ number_format($sub, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada rincian barang yang diterima.</td>
                </tr>
            @endforelse
        </tbody>
        @if($totalNilai > 0)
            <tfoot>
                <tr style="background: #f1f5f9;">
                    <td colspan="5" class="text-end fw-bold">TOTAL ESTIMASI NILAI BARANG MASUK KE GUDANG:</td>
                    <td class="text-end fw-bold text-success">Rp {{ number_format($totalNilai, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="notes-box">
        <div class="notes-title">Catatan Pemeriksaan Fisik Barang (Quality & Quantity Check):</div>
        <div>{{ $gr->notes ?: 'Barang telah diperiksa fisik, kuantiti sesuai, dan telah di-posting menambah stok aktif pada kartu persediaan gudang.' }}</div>
    </div>

    <!-- Signature Matrix -->
    <div class="signature-section">
        <div class="signature-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="signature-box">
                <div class="signature-role">Petugas Penerima Fisik,</div>
                <div class="signature-date">Tgl: {{ $gr->gr_date->format('d/m/Y') }}</div>
                <div class="signature-name">{{ $gr->receiver->full_name ?? 'Storeman' }}</div>
                <div class="signature-title">Warehouse Staff</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Diperiksa Oleh,</div>
                <div class="signature-date">Tgl: {{ $gr->gr_date->format('d/m/Y') }}</div>
                <div class="signature-name">Warehouse Supervisor</div>
                <div class="signature-title">Kepala Gudang Site</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Mengetahui,</div>
                <div class="signature-date">Tgl: {{ $gr->gr_date->format('d/m/Y') }}</div>
                <div class="signature-name">Project / Site Manager</div>
                <div class="signature-title">Penanggung Jawab Operasional</div>
            </div>
        </div>
    </div>
@endsection
