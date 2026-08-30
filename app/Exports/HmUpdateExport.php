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
            ['EX-001', '01 Jan 2026', '12500', '', 'PETUNJUK PENGISIAN:'],
            ['DZ-002', '02 Jan 2026', '8400', '', '1. Unit Code: Harus sesuai dengan kode unit yang ada di sistem.'],
            ['', '', '', '', '2. Tanggal: Gunakan format DD MMM YYYY (contoh: 01 Jan 2026).'],
            ['', '', '', '', '3. HM Value: Berupa angka aktual HM pada tanggal tersebut.'],
            ['', '', '', '', '4. Sistem otomatis melakukan interpolasi jika ada tanggal yang terlewat.'],
        ];
    }

    public function headings(): array
    {
        return [
            'Unit Code',
            'Tanggal (DD MMM YYYY)',
            'HM Value',
            '',
            'INSTRUCTIONS',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Auto-size columns A to E
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF007BFF']]],
            'E1' => ['font' => ['bold' => true, 'color' => ['argb' => 'FF000000']], 'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFFFE699']]],
        ];
    }
}
