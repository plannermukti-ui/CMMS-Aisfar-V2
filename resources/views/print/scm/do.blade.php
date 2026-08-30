@extends('layouts.print', [
    'documentTitle' => 'SURAT JALAN PENGIRIMAN (DELIVERY ORDER)',
    'documentNumber' => $do->do_number,
])

@section('content')
    <div class="doc-header">
        <h2 class="doc-title">SURAT JALAN PENGIRIMAN (DELIVERY ORDER)</h2>
        <div class="doc-number">Nomor Surat Jalan: <strong>{{ $do->do_number }}</strong></div>
        <span class="doc-badge {{ $do->status === 'received' ? 'badge-success' : 'badge-warning' }}">
            Status: {{ $do->status_badge['label'] }}
        </span>
    </div>

    <!-- Meta Information Grid -->
    <div class="meta-box">
        <div class="meta-grid">
            <div>
                <div class="meta-item">
                    <span class="meta-label">Nomor PO Referensi</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value text-primary">{{ $do->purchaseOrder->po_number ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Vendor / Supplier</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $do->purchaseOrder->vendor->name ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Lokasi Asal (Origin)</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $do->origin_location ?: 'Vendor Warehouse' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Tujuan Site / Workshop</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value text-primary">{{ $do->destinationSite->site_name ?? $do->destination_location_name }}</span>
                </div>
            </div>
            <div>
                <div class="meta-item">
                    <span class="meta-label">Tanggal Surat Jalan</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $do->do_date->format('d F Y') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Jasa Ekspedisi / Driver</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $do->expedition_name ?: 'Internal Logistics Driver' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">No. Polisi Kendaraan</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $do->vehicle_plate_number ?: '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">No. Resi / AWB Tracking</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $do->tracking_number ?: '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Estimasi Tiba di Site</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $do->estimated_arrival_date ? $do->estimated_arrival_date->format('d M Y H:i') : '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Shipped Items Table -->
    <table class="table-custom">
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">No.</th>
                <th style="width: 150px;">Part Number</th>
                <th>Nama / Deskripsi Suku Cadang</th>
                <th class="text-center" style="width: 100px;">Qty Dikirim</th>
                <th class="text-center" style="width: 80px;">Satuan</th>
                <th style="width: 150px;">Keterangan Fisik / Kemasan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($do->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $item->part_number }}</td>
                    <td class="fw-semibold">{{ $item->part_name }}</td>
                    <td class="text-center fw-bold fs-7">{{ number_format($item->qty_shipped, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->uom }}</td>
                    <td class="text-muted" style="font-size: 9.5px;">Kondisi Baik & Tersegel</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada rincian barang yang dikirim.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="notes-box">
        <div class="notes-title">Instruksi & Catatan Pengiriman:</div>
        <div>{{ $do->notes ?: 'Harap barang diperiksa kelengkapan fisik dan jumlahnya saat tiba di site workshop sebelum menandatangani Berita Acara Penerimaan (GR).' }}</div>
    </div>

    <!-- Signature Matrix -->
    <div class="signature-section">
        <div class="signature-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="signature-box">
                <div class="signature-role">Pengirim / Logistik,</div>
                <div class="signature-date">Tgl: {{ $do->do_date->format('d/m/Y') }}</div>
                <div class="signature-name">{{ $do->creator->full_name ?? 'Logistics Officer' }}</div>
                <div class="signature-title">Staff Pengiriman HO</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Pengemudi / Ekspedisi,</div>
                <div class="signature-date">Tgl: __ / __ / 202_</div>
                <div class="signature-name">{{ $do->expedition_name ?: 'Driver / Ekspedisi' }}</div>
                <div class="signature-title">Tanda Tangan & Nama Terang</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Penerima Gudang Site,</div>
                <div class="signature-date">Tgl: __ / __ / 202_</div>
                <div class="signature-name">Storeman / Warehouse Site</div>
                <div class="signature-title">Petugas Penerima Fisik</div>
            </div>
        </div>
    </div>
@endsection
