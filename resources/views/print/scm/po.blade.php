@extends('layouts.print', [
    'documentTitle' => 'SURAT PESANAN PEMBELIAN (PURCHASE ORDER)',
    'documentNumber' => $po->po_number,
])

@section('content')
    <div class="doc-header">
        <h2 class="doc-title">SURAT PESANAN PEMBELIAN (PURCHASE ORDER)</h2>
        <div class="doc-number">Nomor PO: <strong>{{ $po->po_number }}</strong></div>
        <span class="doc-badge {{ $po->status === 'received' ? 'badge-success' : ($po->status === 'cancelled' ? 'badge-danger' : 'badge-primary') }}">
            Status: {{ $po->status_badge['label'] }}
        </span>
    </div>

    <!-- Meta Information Grid -->
    <div class="meta-box">
        <div class="meta-grid">
            <div>
                <div class="meta-item">
                    <span class="meta-label">Kepada Vendor</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value text-primary">{{ $po->vendor->name ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Alamat Vendor</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $po->vendor->address ?: 'Alamat tidak tercatat' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">NPWP / Telp</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $po->vendor->npwp ?: '-' }} / {{ $po->vendor->phone ?: '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">No. Pengajuan (PR)</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $po->purchaseRequest->pr_number ?? '-' }}</span>
                </div>
            </div>
            <div>
                <div class="meta-item">
                    <span class="meta-label">Tanggal PO</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $po->po_date->format('d F Y') }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Target Pengiriman</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $po->delivery_target_date ? $po->delivery_target_date->format('d F Y') : 'Sesuai Kesepakatan' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Syarat Pembayaran</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $po->payment_terms ?: 'Net 30 Days' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Disetujui Oleh</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $po->approver->full_name ?? 'Direktur Operasional' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <table class="table-custom">
        <thead>
            <tr>
                <th class="text-center" style="width: 35px;">No.</th>
                <th style="width: 120px;">Part Number</th>
                <th>Deskripsi / Nama Suku Cadang</th>
                <th class="text-center" style="width: 60px;">Qty</th>
                <th class="text-center" style="width: 50px;">UoM</th>
                <th class="text-end" style="width: 110px;">Harga Satuan</th>
                <th class="text-end" style="width: 90px;">Diskon</th>
                <th class="text-end" style="width: 120px;">Subtotal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($po->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $item->part_number }}</td>
                    <td>
                        <span class="fw-semibold">{{ $item->part_name }}</span>
                        @if($item->notes)
                            <div style="font-size: 9px; color: #64748b;">Catatan: {{ $item->notes }}</div>
                        @endif
                    </td>
                    <td class="text-center fw-bold">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->uom }}</td>
                    <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-end text-danger">{{ $item->discount_amount > 0 ? '- Rp ' . number_format($item->discount_amount, 0, ',', '.') : '-' }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada rincian item pesanan.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" rowspan="5" style="vertical-align: top; background: #fafafa; padding: 10px;">
                    <div class="fw-bold mb-1">Syarat & Ketentuan Pembelian (Terms & Conditions):</div>
                    <ol style="margin-left: 16px; font-size: 9.5px; color: #475569; line-height: 1.4;">
                        <li>Barang harus sesuai dengan spesifikasi Part Number dan dalam kondisi 100% Genuine/New.</li>
                        <li>Sertakan Salinan Purchase Order (PO) & Surat Jalan resmi saat pengiriman barang ke gudang/site.</li>
                        <li>Penagihan (Invoice) wajib melampirkan Faktur Pajak, Surat Jalan yang sudah di-GR, dan Kwitansi bermaterai.</li>
                    </ol>
                </td>
                <td class="text-end fw-semibold">Subtotal DPP:</td>
                <td class="text-end fw-bold">Rp {{ number_format($po->subtotal_dpp, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-end fw-semibold text-danger">Potongan Diskon:</td>
                <td class="text-end fw-bold text-danger">- Rp {{ number_format($po->discount_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-end fw-semibold">PPN ({{ $po->ppn_percentage }}%):</td>
                <td class="text-end fw-bold text-primary">+ Rp {{ number_format($po->ppn_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-end fw-semibold">Biaya Kirim:</td>
                <td class="text-end fw-bold">Rp {{ number_format($po->shipping_cost, 0, ',', '.') }}</td>
            </tr>
            <tr style="background: #e2e8f0;">
                <td class="text-end fw-bold" style="font-size: 11px;">GRAND TOTAL:</td>
                <td class="text-end fw-bold" style="font-size: 11px; color: #166534;">Rp {{ number_format($po->grand_total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    @if($po->notes)
        <div class="notes-box">
            <div class="notes-title">Catatan Tambahan:</div>
            <div>{{ $po->notes }}</div>
        </div>
    @endif

    <!-- Signature Matrix -->
    <div class="signature-section">
        <div class="signature-grid">
            <div class="signature-box">
                <div class="signature-role">Dibuat Oleh,</div>
                <div class="signature-date">Tgl: {{ $po->po_date->format('d/m/Y') }}</div>
                <div class="signature-name">Bagian Purchasing</div>
                <div class="signature-title">Procurement Officer</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Diperiksa Oleh,</div>
                <div class="signature-date">Tgl: {{ $po->po_date->format('d/m/Y') }}</div>
                <div class="signature-name">Finance & Accounting</div>
                <div class="signature-title">Finance Manager</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Disetujui Oleh,</div>
                <div class="signature-date">Tgl: {{ $po->po_date->format('d/m/Y') }}</div>
                <div class="signature-name">{{ $po->approver->full_name ?? 'Direktur Operasional' }}</div>
                <div class="signature-title">Authorized Approval</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Konfirmasi Vendor,</div>
                <div class="signature-date">Tgl: __ / __ / 202_</div>
                <div class="signature-name">{{ $po->vendor->name ?? 'Vendor / Supplier' }}</div>
                <div class="signature-title">Tanda Tangan & Cap</div>
            </div>
        </div>
    </div>
@endsection
