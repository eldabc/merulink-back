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
            ['id' => 1], 
            [
                'start' => '2026-05-16',
                'end' => '2026-05-31',
                'status' => 'created',
                'observations' => '',
                'department_id' => 1,
            ]
        );
    }
}
