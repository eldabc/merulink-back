<?php

namespace Database\Seeders;
use Carbon\Carbon;

use App\Models\EmployeePeriod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeePeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EmployeePeriod::firstOrCreate(
            ['employee_id' => 1], 
            [
                'hire_date'   => '2020-10-01',
                'retire_date' => Carbon::now()->day(17),
                'retire_reason' => 'Renuncia',
                'notes' => '',
                
            ]
        );

        EmployeePeriod::firstOrCreate(
            ['employee_id' => 2], 
            [
                'hire_date'   => '2020-10-01',
                'retire_date' => null,
                'retire_reason' => '',
                'notes' => '',
                
            ]
        );

        EmployeePeriod::firstOrCreate(
            ['employee_id' => 3], 
            [
                'hire_date'   => '2020-10-01',
                'retire_date' => null,
                'retire_reason' => '',
                'notes' => '',
                
            ]
        );

        EmployeePeriod::firstOrCreate(
            ['employee_id' => 4], 
            [
                'hire_date'   => '2020-10-01',
                'retire_date' => null,
                'retire_reason' => '',
                'notes' => '',
                
            ]
        );

        EmployeePeriod::firstOrCreate(
            ['employee_id' => 5], 
            [
                'hire_date'   => '2020-10-01',
                'retire_date' => null,
                'retire_reason' => '',
                'notes' => '',
                
            ]
        );

        EmployeePeriod::firstOrCreate(
            ['employee_id' => 6], 
            [
                'hire_date'   => '2020-10-01',
                'retire_date' => null,
                'retire_reason' => '',
                'notes' => '',
                
            ]
        );


        EmployeePeriod::firstOrCreate(
            ['employee_id' => 7], 
            [
                'hire_date'   => '2020-10-01',
                'retire_date' => null,
                'retire_reason' => '',
                'notes' => '',
                
            ]
        );

        // Seguridad
        EmployeePeriod::firstOrCreate(
            ['employee_id' => 8], 
            [
                'hire_date'   => '2020-10-01',
                'retire_date' => null,
                'retire_reason' => '',
                'notes' => '',
                
            ]
        );

        EmployeePeriod::firstOrCreate(
            ['employee_id' => 9], 
            [
                'hire_date'   => '2020-10-01',
                'retire_date' => null,
                'retire_reason' => '',
                'notes' => '',
                
            ]
        );

        EmployeePeriod::firstOrCreate(
            ['employee_id' => 10], 
            [
                'hire_date'   => '2020-10-01',
                'retire_date' => null,
                'retire_reason' => '',
                'notes' => '',
                
            ]
        );

        EmployeePeriod::firstOrCreate(
            ['employee_id' => 11], 
            [
                'hire_date'   => '2020-10-01',
                'retire_date' => null,
                'retire_reason' => '',
                'notes' => '',
                
            ]
        );

        EmployeePeriod::firstOrCreate(
            ['employee_id' => 12], 
            [
                'hire_date'   => '2020-10-01',
                'retire_date' => null,
                'retire_reason' => '',
                'notes' => '',
                
            ]
        );

        EmployeePeriod::firstOrCreate(
            ['employee_id' => 13], 
            [
                'hire_date'   => '2020-10-01',
                'retire_date' => null,
                'retire_reason' => '',
                'notes' => '',
                
            ]
        );
    }
}
