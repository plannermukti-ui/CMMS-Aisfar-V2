<?php

namespace App\Imports;

use App\Models\Equipment;
use App\Services\HmInterpolationService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class HmUpdateImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $interpolationService = app(HmInterpolationService::class);
        $errors = [];
        $rowNum = 1; // +1 for header

        foreach ($rows as $row) {
            $rowNum++;

            // Use first three columns dynamically if headings don't exactly match
            $values = array_values($row->toArray());
            if (count($values) < 3) {
                continue;
            }

            $unitCode = trim($values[0]);
            $dateStr = trim($values[1]);
            $hmValue = trim($values[2]);

            if (empty($unitCode) || empty($dateStr) || empty($hmValue)) {
                continue;
            }

            // Find equipment
            $equipment = Equipment::where('unit', $unitCode)->orWhere('no', $unitCode)->first();
            if (! $equipment) {
                $errors[] = "Baris {$rowNum}: Unit '{$unitCode}' tidak ditemukan.";

                continue;
            }

            // Parse date (DD MMM YYYY or standard excel format)
            try {
                // If it's excel serial date
                if (is_numeric($dateStr)) {
                    $date = Date::excelToDateTimeObject($dateStr)->format('Y-m-d');
                } else {
                    $date = Carbon::parse($dateStr)->format('Y-m-d');
                }
            } catch (Exception $e) {
                $errors[] = "Baris {$rowNum}: Format tanggal '{$dateStr}' tidak valid.";

                continue;
            }

            // Parse HM
            if (! is_numeric($hmValue)) {
                $errors[] = "Baris {$rowNum}: HM harus berupa angka.";

                continue;
            }

            // Validate HM logic (must be >= previous actual HM if exists)
            $lastHm = $equipment->getLastHmBeforeDate($date);
            if ($hmValue < $lastHm) {
                $errors[] = "Baris {$rowNum}: HM pada tanggal ".Carbon::parse($date)->translatedFormat('d M Y')." ({$hmValue}) tidak valid. Harus >= {$lastHm}.";

                continue;
            }

            // Record HM
            $interpolationService->recordHm(
                $equipment->id,
                $date,
                (int) $hmValue,
                'excel',
                auth()->id()
            );
        }

        if (count($errors) > 0) {
            throw new Exception("Beberapa data gagal diproses:\n".implode("\n", $errors));
        }
    }
}
