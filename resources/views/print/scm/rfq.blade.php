@extends('layouts.print', [
    'documentTitle' => 'LEMBAR PENAWARAN & KOMPARASI VENDOR (RFQ QUOTATION)',
    'documentNumber' => $rfq->quotation_number,
    'landscape' => false,
])

@section('content')
    <div class="doc-header">
        <h2 class="doc-title">LEMBAR PENAWARAN HARGA VENDOR (REQUEST FOR QUOTATION)</h2>
        <div class="doc-number">Nomor Quotation: <strong>{{ $rfq->quotation_number }}</strong></div>
        <span class="doc-badge {{ $rfq->is_selected ? 'badge-success' : 'badge-primary' }}">
            {{ $rfq->is_selected ? 'Selected Vendor' : 'Pending' }}
        </span>
    </div>

    <!-- Meta Information Grid -->
    <div class="meta-box">
        <div class="meta-grid">
            <div>
                <div class="meta-item">
                    <span class="meta-label">Vendor / Supplier</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value text-primary">{{ $rfq->vendor->name ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">No. Pengajuan PR</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $rfq->purchaseRequest->pr_number ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Estimasi Waktu Kirim</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $rfq->delivery_lead_time_days ? $rfq->delivery_lead_time_days . ' Hari Kerja' : 'Ready Stock' }}</span>
                </div>
            </div>
            <div>
                <div class="meta-item">
                    <span class="meta-label">Tanggal Penawaran</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $rfq->created_at->format('d F Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quotation Items Table -->
    <table class="table-custom">
        <thead>
            <tr>
                <th class="text-center" style="width: 35px;">No.</th>
                <th>Harga Satuan</th>
                <th class="text-end" style="width: 85px;">Diskon</th>
                <th class="text-end" style="width: 120px;">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rfq->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-end text-danger">{{ $item->discount_amount > 0 ? '- Rp ' . number_format($item->discount_amount, 0, ',', '.') : '-' }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada rincian item penawaran.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" rowspan="4" style="vertical-align: top; background: #fafafa; padding: 8px;">
                    <div class="fw-bold mb-1">Catatan Evaluasi Penawaran (Commercial Evaluation):</div>
                    <div style="font-size: 9.5px; color: #475569; line-height: 1.4;">
                        {{ $rfq->notes ?: 'Penawaran ini telah dievaluasi dari segi harga kompetitif, keaslian spesifikasi suku cadang, dan lead time pengiriman ke site tambang.' }}
                    </div>
                </td>
                <td class="text-end fw-semibold">Subtotal:</td>
                <td class="text-end fw-bold">Rp {{ number_format($rfq->subtotal_dpp, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-end fw-semibold">PPN ({{ $rfq->ppn_percentage }}%):</td>
                <td class="text-end fw-bold text-primary">+ Rp {{ number_format($rfq->ppn_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="text-end fw-semibold">Ongkos Kirim:</td>
                <td class="text-end fw-bold">Rp {{ number_format($rfq->shipping_cost, 0, ',', '.') }}</td>
            </tr>
            <tr style="background: #e2e8f0;">
                <td class="text-end fw-bold">TOTAL PENAWARAN:</td>
                <td class="text-end fw-bold text-success" style="font-size: 11px;">Rp {{ number_format($rfq->grand_total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Signature Matrix -->
    <div class="signature-section">
        <div class="signature-grid">
            <div class="signature-box">
                <div class="signature-role">Dibuat Oleh,</div>
                <div class="signature-date">Tgl: {{ $rfq->created_at->format('d/m/Y') }}</div>
                <div class="signature-name">Buyer / Purchasing</div>
                <div class="signature-title">Procurement Staff</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Diperiksa Oleh,</div>
                <div class="signature-date">Tgl: {{ $rfq->created_at->format('d/m/Y') }}</div>
                <div class="signature-name">Procurement Head</div>
                <div class="signature-title">Manager Pengadaan</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Disetujui Oleh,</div>
                <div class="signature-date">Tgl: {{ $rfq->created_at->format('d/m/Y') }}</div>
                <div class="signature-name">Direktur Operasional</div>
                <div class="signature-title">Operational Director</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Perwakilan Vendor,</div>
                <div class="signature-date">Tgl: __ / __ / 202_</div>
                <div class="signature-name">{{ $rfq->vendor->name ?? 'Vendor Representative' }}</div>
                <div class="signature-title">Tanda Tangan & Cap</div>
            </div>
        </div>
    </div>
@endsection
