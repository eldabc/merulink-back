<?php

namespace Database\Seeders;
use Carbon\Carbon;

use App\Models\Employee;
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
        // Crea un período laboral por cada empleado existente.
        // Los IDs los define EmployeeSeeder;
        $employees = Employee::orderBy('id')->get();

        foreach ($employees as $index => $employee) {
            // El primer empleado lleva un ejemplo de baja
            $isFirst = $index === 0;

            EmployeePeriod::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'hire_date'     => '2020-10-01',
                    'retire_date'   => $isFirst ? Carbon::now()->day(17) : null,
                    'retire_reason' => $isFirst ? 'Renuncia' : '',
                    'notes'         => '',
                ]
            );
        }
    }
}
