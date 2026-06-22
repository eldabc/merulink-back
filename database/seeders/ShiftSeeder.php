<?php

namespace Database\Seeders;
use Carbon\Carbon;

use App\Models\Shift;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shift::firstOrCreate(
            ['code' => 'AD-01'], 
            [
                'description' => 'Administrativo Shift',
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
                'available' => 'yes',
                'available_from' => now()->format('Y-m-d'),
                'observation' => 'Test Shift',
                'department_id' => 1,
            ]
        );

        Shift::firstOrCreate(
            ['code' => 'SE-01'], 
            [
                'description' => 'Operative Shift',
                'night_shift' => 'day',
                'type_shift' => 'operative',
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
                'available' => 'yes',
                'available_from' => now()->format('Y-m-d'),
                'observation' => 'Test Shift',
                'department_id' => 7,
            ]
        );
    }
}
