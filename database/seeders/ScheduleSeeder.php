<?php

namespace Database\Seeders;

use App\Models\Shift;
use App\Models\Schedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shift::firstOrCreate(
            ['code' => '1234'], 
            [
                'description' => 'Test Shift',
                'night_shift' => 'Diurno',
                'type_shift' => 'administrative',
                'check_in_time' => '08:00:00',
                'check_out_time' => '17:00:00',
                'time_rest_period' => 1,
                'duration_unit_rest_period' => 'hours',
                'time_active_period' => 8,
                'duration_unit_active_period' => 'hours',
                'time_total_period' => 9,
                'duration_unit_total_period' => 'hours',
                'allow_exit' => 'yes',
                'allow_re_scanned' => 'no',
                'available' => 'yes',
                'observation' => 'Test Shift',
                'department_id' => 1,
            ]
        );

        Schedule::firstOrCreate(
            ['observation' => 'Test Schedule'], 
            [
                'shift_id' => 1,
            ]
        );
    }
}
