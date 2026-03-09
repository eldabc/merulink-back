<?php

namespace Database\Seeders;

use App\Models\Locker;
use App\Models\Padlock;
use App\Models\Employee;
use App\Models\Assign;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Primer registro disponible de cada tabla
        $locker = Locker::first();
        $padlock = Padlock::first();
        $employee = Employee::first();

        if ($locker && $padlock && $employee) {
            Assign::create([
                'assign_code' => 'ASG'. $locker->code .'-'. now()->format('d-m-Y'),
                'assign_date' => now()->format('Y-m-d'), 
                'locker_id'   => $locker->id,
                'padlock_id'  => $padlock->id,
                'employee_id' => $employee->id,
            ]);
        }
    }
}
