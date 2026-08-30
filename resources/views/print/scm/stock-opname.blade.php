@extends('layouts.print', [
    'documentTitle' => 'BERITA ACARA STOCK OPNAME GUDANG',
    'documentNumber' => $opname->opname_number,
    'landscape' => true,
])

@section('content')
    <div class="doc-header">
        <h2 class="doc-title">BERITA ACARA HASIL STOCK OPNAME FISIK GUDANG</h2>
        <div class="doc-number">Nomor Opname: <strong>{{ $opname->opname_number }}</strong></div>
        <span class="doc-badge {{ $opname->status === 'approved' ? 'badge-success' : 'badge-warning' }}">
            Status: {{ $opname->status_badge['label'] }}
        </span>
    </div>

    <!-- Meta Information Grid -->
    <div class="meta-box">
        <div class="meta-grid-3">
            <div>
                <div class="meta-item">
                    <span class="meta-label" style="width: 100px;">Lokasi Site</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value text-primary">{{ $opname->site->site_name ?? 'Central Warehouse' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label" style="width: 100px;">Tanggal Opname</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $opname->opname_date ? $opname->opname_date->format('d F Y') : $opname->created_at->format('d F Y') }}</span>
                </div>
            </div>
            <div>
                <div class="meta-item">
                    <span class="meta-label" style="width: 110px;">Pelaksana Hitung</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $opname->conductedBy->full_name ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label" style="width: 110px;">Pemeriksa / Audit</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $opname->approver->full_name ?? 'Internal Audit' }}</span>
                </div>
            </div>
            <div>
                <div class="meta-item">
                    <span class="meta-label" style="width: 120px;">Total Item Dihitung</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ count($opname->items) }} Item</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label" style="width: 120px;">Total Net Selisih</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value {{ $opname->total_variance_value < 0 ? 'text-danger' : 'text-success' }}">
                        Rp {{ number_format($opname->total_variance_value ?? 0, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Opname Items Table -->
    <table class="table-custom">
        <thead>
            <tr>
                <th class="text-center" style="width: 35px;">No.</th>
                <th style="width: 130px;">Part Number</th>
                <th>Nama / Deskripsi Suku Cadang</th>
                <th class="text-center" style="width: 90px;">Lokasi Rak</th>
                <th class="text-center" style="width: 45px;">UoM</th>
                <th class="text-center" style="width: 80px;">Stok Sistem</th>
                <th class="text-center" style="width: 80px;">Stok Fisik</th>
                <th class="text-center" style="width: 75px;">Selisih</th>
                <th class="text-end" style="width: 110px;">Harga Satuan</th>
                <th class="text-end" style="width: 120px;">Nilai Selisih</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalShortage = 0;
                $totalSurplus = 0;
            @endphp
            @forelse($opname->items as $index => $item)
                @php
                    $diff = (float)$item->physical_stock - (float)$item->system_stock;
                    $varVal = $diff * (float)($item->part->standard_cost ?? 0);
                    if ($varVal < 0) { $totalShortage += abs($varVal); }
                    else { $totalSurplus += $varVal; }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $item->part->part_number ?? $item->part_number }}</td>
                    <td class="fw-semibold">{{ $item->part->name ?? $item->part_name }}</td>
                    <td class="text-center text-muted" style="font-size: 9px;">{{ $item->rack_location ?? '-' }}</td>
                    <td class="text-center">{{ $item->part->uom ?? 'Pcs' }}</td>
                    <td class="text-center">{{ number_format($item->system_stock, 0, ',', '.') }}</td>
                    <td class="text-center fw-bold">{{ number_format($item->physical_stock, 0, ',', '.') }}</td>
                    <td class="text-center fw-bold {{ $diff < 0 ? 'text-danger' : ($diff > 0 ? 'text-success' : 'text-muted') }}">
                        {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 0, ',', '.') }}
                    </td>
                    <td class="text-end">Rp {{ number_format($item->part->standard_cost ?? 0, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold {{ $varVal < 0 ? 'text-danger' : ($varVal > 0 ? 'text-success' : 'text-muted') }}">
                        {{ $varVal > 0 ? '+' : '' }}Rp {{ number_format($varVal, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada rincian data stock opname.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f1f5f9;">
                <td colspan="7" class="text-end fw-bold">TOTAL REKAPITULASI SELISIH OPNAME:</td>
                <td colspan="3" class="text-end fw-bold">
                    <span class="text-success me-3">Surplus: +Rp {{ number_format($totalSurplus, 0, ',', '.') }}</span> |
                    <span class="text-danger ms-3">Shortage: -Rp {{ number_format($totalShortage, 0, ',', '.') }}</span>
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="notes-box">
        <div class="notes-title">Catatan & Rekomendasi Penyesuaian (Adjustment Remarks):</div>
        <div>{{ $opname->notes ?: 'Perhitungan fisik telah dilakukan secara bersama oleh tim pelaksana gudang dan saksi audit. Penyesuaian stok sistem akan dilakukan setelah persetujuan manajemen.' }}</div>
    </div>

    <!-- Signature Matrix -->
    <div class="signature-section">
        <div class="signature-grid">
            <div class="signature-box">
                <div class="signature-role">Petugas Penghitung (Counter),</div>
                <div class="signature-date">Tgl: {{ $opname->created_at->format('d/m/Y') }}</div>
                <div class="signature-name">{{ $opname->conductedBy->full_name ?? 'Tim Opname' }}</div>
                <div class="signature-title">Warehouse Counter</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Kepala Gudang Site,</div>
                <div class="signature-date">Tgl: {{ $opname->created_at->format('d/m/Y') }}</div>
                <div class="signature-name">Warehouse Supervisor</div>
                <div class="signature-title">Penanggung Jawab Gudang</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Saksi / Internal Audit,</div>
                <div class="signature-date">Tgl: {{ $opname->created_at->format('d/m/Y') }}</div>
                <div class="signature-name">{{ $opname->approver->full_name ?? 'Internal Audit' }}</div>
                <div class="signature-title">Audit Controller</div>
            </div>
            <div class="signature-box">
                <div class="signature-role">Mengetahui & Menyetujui,</div>
                <div class="signature-date">Tgl: {{ $opname->created_at->format('d/m/Y') }}</div>
                <div class="signature-name">Project / Site Manager</div>
                <div class="signature-title">Pimpinan Operasional Site</div>
            </div>
        </div>
    </div>
@endsection
