@extends('layouts.print', [
    'documentTitle' => 'COMPONENT TRACKING REPORT',
    'documentNumber' => $component->component_code,
])

@section('content')
    <div class="doc-header">
        <h2 class="doc-title">COMPONENT TRACKING REPORT</h2>
        <div class="doc-number">Kode Komponen: <strong>{{ $component->component_code }}</strong></div>
        <span class="doc-badge {{ $component->status === 'installed' ? 'badge-success' : 'badge-primary' }}">
            Status: {{ $component->status_badge['label'] }}
        </span>
    </div>

    <!-- Meta Information Grid -->
    <div class="meta-box">
        <div class="meta-grid">
            <div>
                <div class="meta-item">
                    <span class="meta-label">Nama Komponen</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value text-primary">{{ $component->name }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Jenis Komponen</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ ucfirst(str_replace('_', ' ', $component->component_type)) }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Serial Number</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $component->serial_number ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Brand / Model</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $component->brand_model ?? '-' }}</span>
                </div>
            </div>
            <div>
                <div class="meta-item">
                    <span class="meta-label">Unit / Equipment</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value text-primary">{{ $component->equipment->unit ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Posisi / Pemasangan</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $component->position ?? '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Tanggal Dipasang</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ $component->installed_date ? $component->installed_date->format('d F Y') : '-' }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">HM Saat Dipasang</span>
                    <span class="meta-separator">:</span>
                    <span class="meta-value">{{ number_format($component->installed_at_hm, 0, ',', '.') }} HM</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Life Progress -->
    <div class="notes-box">
        <div class="notes-title">Akumulasi Jam / Accumulated Hours:</div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px;">
            <span class="fw-semibold">{{ number_format($component->accumulated_hours, 0, ',', '.') }} / {{ number_format($component->target_life_hours, 0, ',', '.') }} jam</span>
            <span class="fw-bold {{ $component->life_percentage > 80 ? 'text-danger' : ($component->life_percentage > 50 ? 'text-warning' : 'text-success') }}">
                {{ $component->life_percentage }}%
            </span>
        </div>
        <div style="background: #e5e7eb; border-radius: 4px; height: 12px; margin-top: 6px; overflow: hidden;">
            <div style="background: {{ $component->life_percentage > 80 ? '#dc2626' : ($component->life_percentage > 50 ? '#d97706' : '#16a34a') }}; height: 100%; width: {{ $component->life_percentage }}%; border-radius: 4px;"></div>
        </div>
    </div>

    @if($component->remarks)
        <div class="notes-box">
            <div class="notes-title">Catatan / Remarks:</div>
            <div>{{ $component->remarks }}</div>
        </div>
    @endif

    <!-- Condition Reports History -->
    @if($component->conditionReports->count())
        <h3 style="font-size: 12px; font-weight: 700; margin: 12px 0 6px; color: #0f172a;">Riwayat Kondisi Komponen (CCR History)</h3>
        <table class="table-custom">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No.</th>
                    <th>Tanggal</th>
                    <th>Nomor CCR</th>
                    <th class="text-center">Wear %</th>
                    <th>Rekomendasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($component->conditionReports as $index => $ccr)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $ccr->ccr_date->format('d/m/Y') }}</td>
                        <td class="fw-bold">{{ $ccr->ccr_number }}</td>
                        <td class="text-center fw-bold">{{ $ccr->wear_percentage }}%</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $ccr->recommendation)) }}</td>
                        <td>{{ $ccr->status_badge['label'] ?? ucfirst($ccr->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Outside Repairs History -->
    @if($component->outsideRepairs->count())
        <h3 style="font-size: 12px; font-weight: 700; margin: 12px 0 6px; color: #0f172a;">Riwayat Perbaikan Luar (OSR History)</h3>
        <table class="table-custom">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No.</th>
                    <th>Tanggal</th>
                    <th>Nomor OSR</th>
                    <th>Vendor</th>
                    <th>Estimasi Biaya</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($component->outsideRepairs as $index => $osr)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $osr->order_date->format('d/m/Y') }}</td>
                        <td class="fw-bold">{{ $osr->osr_number }}</td>
                        <td>{{ $osr->vendor->name ?? '-' }}</td>
                        <td class="text-end">Rp {{ number_format($osr->estimated_cost, 0, ',', '.') }}</td>
                        <td>{{ $osr->status_badge['label'] ?? ucfirst($osr->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Failure Reports History -->
    @if($component->failureReports->count())
        <h3 style="font-size: 12px; font-weight: 700; margin: 12px 0 6px; color: #0f172a;">Riwayat Failure Analysis (FAR History)</h3>
        <table class="table-custom">
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No.</th>
                    <th>Tanggal</th>
                    <th>Nomor FAR</th>
                    <th>Judul Failure</th>
                    <th>Jenis Failure</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($component->failureReports as $index => $far)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $far->incident_date->format('d/m/Y') }}</td>
                        <td class="fw-bold">{{ $far->far_number }}</td>
                        <td>{{ $far->failure_title }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $far->failure_type)) }}</td>
                        <td>{{ $far->status_badge['label'] ?? ucfirst($far->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="signature-box">
                <div class="signature-role">Diketahui Oleh,</div>
                <div class="signature-date">Tgl: __ / __ / 202_</div>
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
