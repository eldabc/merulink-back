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
        $first_employee = Employee::firstOrCreate(
            ['ci' => '21378987'], 
            [
                'num_employee'   => '100',
                'first_name' => 'Ana',
                'second_name' => 'Camila',
                'last_name' => 'Bello',
                'second_last_name' => 'Pérez',
                'birthdate' => '1994-03-11',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'M',
                'marital_status' => 'Soltero',
                'blood_type' => 'O+',
                'email' => 'ana.camila@gmail.com',
                'mobile_phone' => '04121234567',
                'home_phone' => '02863410565',
                'address' => 'PZO',
                'join_date' => '2025-10-02',
                'department_id' => 3,
                'position_id' => 3,
                'user_name' => 'ana.camila',
                'user_pass' => '1234hgfd3-',
                'change_pass_next_login' => true,
                'status' => true,
                'use_meru_link' => true,
                'use_hid_card' => true,
                'use_locker' => true,
                'use_transport' => true,
                
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
                'birthdate' => '1994-03-01',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'H',
                'marital_status' => 'Soltero',
                'blood_type' => 'O+',
                'email' => 'jose.bello@gmail.com',
                'mobile_phone' => '04121234567',
                'home_phone' => '02863410565',
                'address' => 'PZO',
                'join_date' => '2025-01-02',
                'department_id' => 1,
                'position_id' => 1,
                'user_name' => 'jose.bello',
                'user_pass' => '1234hgfd3-',
                'change_pass_next_login' => true,
                'status' => true,
                'use_meru_link' => true,
                'use_hid_card' => false,
                'use_locker' => false,
                'use_transport' => false,
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
                'birthdate' => '1991-03-01',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'H',
                'marital_status' => 'Soltero',
                'blood_type' => 'O+',
                'email' => 'angel.carrillo@gmail.com',
                'mobile_phone' => '04121234567',
                'home_phone' => '02863410565',
                'address' => 'PZO',
                'join_date' => '2024-10-02',
                'department_id' => 1,
                'position_id' => 2,
                'user_name' => 'angel.carrillo',
                'user_pass' => '1234hgfd3-',
                'change_pass_next_login' => true,
                'status' => true,
                'use_meru_link' => true,
                'use_hid_card' => false,
                'use_locker' => false,
                'use_transport' => false,
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
                'birthdate' => '1991-04-01',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'H',
                'marital_status' => 'Soltero',
                'blood_type' => 'O+',
                'email' => 'manuel.fer@gmail.com',
                'mobile_phone' => '04121234567',
                'home_phone' => '02863410565',
                'address' => 'PZO',
                'join_date' => '2020-10-02',
                'department_id' => 4,
                'position_id' => 4,
                'user_name' => 'manuel.fer',
                'user_pass' => '1234hgfd3-',
                'change_pass_next_login' => true,
                'status' => true,
                'use_meru_link' => true,
                'use_hid_card' => false,
                'use_locker' => false,
                'use_transport' => false,
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
                'birthdate' => '1991-03-01',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'M',
                'marital_status' => 'Soltero',
                'blood_type' => 'O+',
                'email' => 'maria.her@gmail.com',
                'mobile_phone' => '04121234567',
                'home_phone' => '02863410565',
                'address' => 'PZO',
                'join_date' => '2015-10-02',
                'department_id' => 4,
                'position_id' => 5,
                'user_name' => 'maria.her',
                'user_pass' => '1234hgfd3-',
                'change_pass_next_login' => true,
                'status' => true,
                'use_meru_link' => true,
                'use_hid_card' => false,
                'use_locker' => false,
                'use_transport' => false,
            ]
        );
    }
}
