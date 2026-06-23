<?php

namespace Database\Seeders;
use Carbon\Carbon;

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
        $start = Carbon::now()->startOfMonth();
        $end = $start->copy()->addDays(14);

         SchedulePlanning::firstOrCreate(
            ['start' => $start, 'end' => $end], 
            [
                'month_number' => 6,
                'status' => 'created',
                'observations' => 'Observations Test',
                'department_id' => 1,
            ]
        );
    }
}
