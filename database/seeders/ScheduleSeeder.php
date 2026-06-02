<?php

namespace Database\Seeders;
use Carbon\Carbon;

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

        $today = Carbon::now()->startOfDay();

        Schedule::firstOrCreate(
            ['date' => $today], 
            [
                'employee_id' => 1,
                'shift_id' => $shift->id,
                'letter_shift' => 'A',
                'color' => '#FBBD08',
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
