<?php

namespace Database\Seeders;

use App\Imports\PadlockImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Padlock;

class PadlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = storage_path('app/padlocks.xls');

        if (!file_exists($filePath)) {
            $this->command?->warn("Archivo {$filePath} no encontrado. Saltando PadlockSeeder.");
            return;
        }

        Excel::import(new PadlockImport, $filePath);
    }
}
