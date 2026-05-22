<?php

namespace Database\Seeders;

use App\Models\Vacation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VacationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vacation::firstOrCreate(
            ['start' => '2026-05-16', 'end' => '2026-05-26'],
            [
                'type' => 'vacation',
                'observations' => 'Test Vacation',
                'employee_id' => 1,
            ]
        );
    }
}
