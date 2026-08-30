<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HmUpdateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            ['EX-001', '01 Jan 2026', '12500'],
            ['DZ-002', '02 Jan 2026', '8400'],
        ];
    }

    public function headings(): array
    {
        return [
            'Unit Code',
            'Tanggal (DD MMM YYYY)',
            'HM Value',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF007BFF']]],
        ];
    }
}
