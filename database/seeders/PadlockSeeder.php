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
        Excel::import(new PadlockImport, storage_path('app\padlocks.xls'));

        Padlock::firstOrCreate(
            ['serial' => '123457'], 
            [
             'pass'   => '11-12-77',
             'status' => 'Disponible',
             'padlock_pattern_id' => 2,
            ]
        );

    }
}
