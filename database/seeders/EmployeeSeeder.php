<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $padlock = Employee::firstOrCreate(
            ['ci' => '21378987'], 
            [
                'num_employee'   => '100',
                'first_name' => 'José',
                'second_name' => 'Ramón',
                'last_name' => 'Bello',
                'second_last_name' => 'Pérez',
                'birthDate' => '1994-03-01',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'H',
                'marital_status' => 'Soltero',
            ]
        );
    }
}
