<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmergencyContact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmergencyContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $first_employee = Employee::first();

        EmergencyContact::create([
            'name' => 'Maria',
            'last_name' => 'Rodríguez',
            'relationship' => 'Hermana',
            'phone' => '04121234567',
            'address' => 'PZO',
            'employee_id' => $first_employee->id,
        ]);

        EmergencyContact::create([
            'name' => 'Pedro',
            'last_name' => 'Rodríguez',
            'relationship' => 'Hermano',
            'phone' => '04241234567',
            'address' => 'PZO',
            'employee_id' => $first_employee->id,
        ]);

         EmergencyContact::create([
            'name' => 'Manuel',
            'last_name' => 'Rodríguez',
            'relationship' => 'Cuñado',
            'phone' => '04161234567',
            'address' => 'PZO',
            'employee_id' => $first_employee->id,
        ]);
    }
}
