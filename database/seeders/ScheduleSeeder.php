<?php

namespace Database\Seeders;

use App\Models\Shift;
use App\Models\Schedule;
use App\Models\SchedulePlanning;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shift = Shift::first();
        $schedulePlanning = SchedulePlanning::first();

        Schedule::firstOrCreate(
            ['date' => '2026-06-16'], 
            [
                'employee_id' => 2,
                'shift_id' => $shift->id,
                'letter_shift' => 'A',
                'color' => '#000000',
                'code' => 'AD-01',
                'night_shift' => 'day',
                'type_shift' => 'administrative',
                'check_in_time' => '08:00:00',
                'check_out_time' => '17:00:00',
                'rest_period_time' => 1,
                'rest_period_unit_time' => 'hours',
                'active_period_time' => 8,
                'active_period_unit_time' => 'hours',
                'total_period_time' => 9,
                'total_period_unit_time' => 'hours',
                'allow_exit' => 'yes',
                'allow_re_scanned' => 'no',

                'schedule_planning_id' => $schedulePlanning->id,
            ]
        );
    }
}
