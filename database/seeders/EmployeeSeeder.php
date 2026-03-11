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
        Employee::firstOrCreate(
            ['ci' => '21378987'], 
            [
                'num_employee'   => '100',
                'first_name' => 'Ana',
                'second_name' => 'Camila',
                'last_name' => 'Bello',
                'second_last_name' => 'Pérez',
                'birthDate' => '1994-03-11',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'M',
                'marital_status' => 'Soltera',
                'department_id' => 3,
            ]
        );

        Employee::firstOrCreate(
            ['ci' => '21378988'], 
            [
                'num_employee'   => '101',
                'first_name' => 'José',
                'second_name' => 'Ramón',
                'last_name' => 'Bello',
                'second_last_name' => 'Pérez',
                'birthDate' => '1994-03-01',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'H',
                'marital_status' => 'Soltero',
                'department_id' => 1,
            ]
        );

        Employee::firstOrCreate(
            ['ci' => '21378989'], 
            [
                'num_employee'   => '102',
                'first_name' => 'Angel',
                'second_name' => 'Andrés',
                'last_name' => 'Carrillo',
                'second_last_name' => 'Pérez',
                'birthDate' => '1991-03-01',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'H',
                'marital_status' => 'Soltero',
                'department_id' => 1,
            ]
        );

        Employee::firstOrCreate(
            ['ci' => '22378929'], 
            [
                'num_employee'   => '104',
                'first_name' => 'Manuel',
                'second_name' => 'Jonh',
                'last_name' => 'Fernández',
                'second_last_name' => 'Pérez',
                'birthDate' => '1991-04-01',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'H',
                'marital_status' => 'Soltera',
                'department_id' => 4,
            ]
        );

        Employee::firstOrCreate(
            ['ci' => '21378919'], 
            [
                'num_employee'   => '103',
                'first_name' => 'María',
                'second_name' => 'Josefa',
                'last_name' => 'Hernández',
                'second_last_name' => 'Pérez',
                'birthDate' => '1991-03-01',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'M',
                'marital_status' => 'Soltera',
                'department_id' => 4,
            ]
        );
    }
}
