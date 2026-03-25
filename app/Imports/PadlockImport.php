<?php

namespace App\Imports;

use App\Models\Padlock;
use App\Enums\PadlockStatus;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PadlockImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            1 => new SecondSheetImport(), // Hoja 2
        ];
    }    
}

class SecondSheetImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Saltar encabezados si existen

            Padlock::create([
                'serial' => $row[0],
                'pass' => $row[1],
                'status' => PadlockStatus::AVAILABLE,
                'padlock_pattern_id' => 1
            ]);
        }
    }
}
