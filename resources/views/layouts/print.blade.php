<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $documentTitle ?? 'Dokumen Cetak' }} - {{ $companyName ?? 'CMMS' }}</title>
    
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-light: #eff6ff;
            --gray-900: #111827;
            --gray-800: #1f2937;
            --gray-700: #374151;
            --gray-600: #4b5563;
            --gray-500: #6b7280;
            --gray-400: #9ca3af;
            --gray-300: #d1d5db;
            --gray-200: #e5e7eb;
            --gray-100: #f3f4f6;
            --gray-50: #f9fafb;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: var(--gray-900);
            background-color: #525659;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Top Action Bar (Screen Only) */
        .print-toolbar {
            position: sticky;
            top: 0;
            z-index: 9999;
            background: #1e293b;
            color: #ffffff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .toolbar-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .toolbar-title {
            font-size: 14px;
            font-weight: 600;
        }

        .toolbar-badge {
            background: rgba(255, 255, 255, 0.15);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 500;
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-print {
            background: #2563eb;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }

        .btn-print:hover {
            background: #1d4ed8;
        }

        .btn-close-window {
            background: rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-close-window:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
        }

        /* Printable Document Container */
        .sheet-container {
            display: flex;
            justify-content: center;
            padding: 30px 15px;
        }

        .sheet {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            padding: 18mm 20mm 20mm 20mm;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .sheet.landscape {
            width: 297mm;
            min-height: 210mm;
        }

        /* Letterhead Component */
        .letterhead-wrapper {
            margin-bottom: 16px;
        }

        .letterhead-main {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 12px;
        }

        .letterhead-logo {
            flex-shrink: 0;
            width: 90px;
            max-height: 75px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .letterhead-logo img {
            max-width: 100%;
            max-height: 75px;
            object-fit: contain;
        }

        .letterhead-logo-placeholder {
            width: 75px;
            height: 75px;
            background: var(--primary-light);
            border: 2px solid var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 800;
            color: var(--primary);
        }

        .letterhead-text {
            flex-grow: 1;
            text-align: center;
        }

        .letterhead-company-name {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .letterhead-company-sub {
            font-size: 10px;
            font-weight: 700;
            color: #475569;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .letterhead-company-address {
            font-size: 9.5px;
            color: #475569;
            line-height: 1.4;
            max-width: 90%;
            margin: 0 auto;
        }

        .letterhead-divider {
            border-top: 3px double #0f172a;
            margin-top: 2px;
            margin-bottom: 14px;
        }

        /* Document Title Header */
        .doc-header {
            text-align: center;
            margin-bottom: 18px;
            position: relative;
        }

        .doc-title {
            font-size: 15px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }

        .doc-number {
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            margin-top: 2px;
        }

        .doc-badge {
            display: inline-block;
            margin-top: 4px;
            padding: 2px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: var(--gray-100);
            border: 1px solid var(--gray-300);
        }

        .doc-badge.badge-success { background: #dcfce7; color: #166534; border-color: #86efac; }
        .doc-badge.badge-warning { background: #fef3c7; color: #92400e; border-color: #fde68a; }
        .doc-badge.badge-primary { background: #dbeafe; color: #1e40af; border-color: #93c5fd; }
        .doc-badge.badge-danger { background: #fee2e2; color: #991b1b; border-color: #fca5a5; }

        /* Meta Grid Information */
        .meta-box {
            background: #ffffff;
            border: 1px solid var(--gray-300);
            border-radius: 4px;
            margin-bottom: 14px;
            padding: 10px 12px;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px 20px;
        }

        .meta-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px 16px;
        }

        .meta-item {
            display: flex;
            font-size: 11px;
        }

        .meta-label {
            width: 130px;
            flex-shrink: 0;
            color: var(--gray-600);
            font-weight: 500;
        }

        .meta-separator {
            width: 14px;
            flex-shrink: 0;
            color: var(--gray-500);
        }

        .meta-value {
            flex-grow: 1;
            font-weight: 600;
            color: var(--gray-900);
        }

        /* Standard Structured Tables */
        .table-custom {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 10.5px;
        }

        .table-custom th,
        .table-custom td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            vertical-align: middle;
        }

        .table-custom th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9.5px;
            letter-spacing: 0.3px;
        }

        .table-custom tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-right, .text-end { text-align: right; }
        .text-left, .text-start { text-align: left; }
        .fw-bold { font-weight: 700; }
        .fw-semibold { font-weight: 600; }

        /* Notes Box */
        .notes-box {
            border: 1px dashed var(--gray-300);
            background: #fafafa;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 14px;
            font-size: 10.5px;
        }

        .notes-title {
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 2px;
        }

        /* Signatures Grid */
        .signature-section {
            margin-top: 24px;
            page-break-inside: avoid;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 12px;
            text-align: center;
        }

        .signature-box {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            border-radius: 4px;
            padding: 8px 6px;
        }

        .signature-role {
            font-size: 10px;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .signature-date {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 38px;
        }

        .signature-name {
            font-size: 10.5px;
            font-weight: 700;
            color: #0f172a;
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
            margin: 0 8px;
        }

        .signature-title {
            font-size: 9px;
            color: #64748b;
        }

        /* Footer */
        .doc-footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 8.5px;
            color: var(--gray-500);
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }

            .no-print, .print-toolbar {
                display: none !important;
            }

            .sheet-container {
                padding: 0 !important;
            }

            .sheet {
                width: 100% !important;
                min-height: auto !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            @page {
                size: A4 portrait;
                margin: 12mm 15mm 15mm 15mm;
            }

            .sheet.landscape {
                @page {
                    size: A4 landscape;
                    margin: 12mm 15mm 15mm 15mm;
                }
            }
        }
    </style>
</head>
<body>
    <!-- Top Action Bar (Hidden on Print) -->
    <div class="print-toolbar no-print">
        <div class="toolbar-info">
            <span class="toolbar-title">{{ $documentTitle ?? 'Dokumen' }}</span>
            <span class="toolbar-badge">{{ $documentNumber ?? '-' }}</span>
        </div>
        <div class="toolbar-actions">
            <button type="button" onclick="window.print()" class="btn-print">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Dokumen / PDF
            </button>
            <button type="button" onclick="window.close()" class="btn-close-window">Tutup Jendela</button>
        </div>
    </div>

    <!-- Printable Content Sheet -->
    <div class="sheet-container">
        <div class="sheet {{ $landscape ?? false ? 'landscape' : '' }}">
            <!-- Corporate Letterhead -->
            @include('components.print-letterhead')

            <!-- Document Body Content -->
            @yield('content')

            <!-- Document Footer -->
            <div class="doc-footer">
                <div>Dicetak otomatis melalui CMMS System pada {{ now()->format('d F Y, H:i:s') }} WIB</div>
                <div>Halaman 1 / 1 (Dokumen Sah Internal Perusahaan)</div>
            </div>
        </div>
    </div>
</body>
</html>
