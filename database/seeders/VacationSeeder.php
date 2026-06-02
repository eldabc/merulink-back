<?php

namespace Database\Seeders;
use Carbon\Carbon;

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

        $today = Carbon::now()->startOfDay();
        $start = $today->copy()->addDays(1);
        $end = $start->copy()->addDays(10);

        Vacation::firstOrCreate(
            ['start' => $start, 'end' => $end],
            [
                'type' => 'vacation',
                'observations' => 'Test Vacation',
                'employee_id' => 2,
            ]
        );
    }
}
