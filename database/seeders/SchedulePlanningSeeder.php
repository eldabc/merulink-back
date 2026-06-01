<?php

namespace Database\Seeders;

use App\Models\SchedulePlanning;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SchedulePlanningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         SchedulePlanning::firstOrCreate(
            ['start' => '2026-06-16'], 
            [
                'end' => '2026-06-30',
                'month_number' => 6,
                'fortnight_number' => 2,
                'status' => 'created',
                'observations' => 'Observations Test',
                'department_id' => 1,
            ]
        );
    }
}
