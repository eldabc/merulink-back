<?php

namespace Database\Seeders;
use Carbon\Carbon;

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
        $startMonth = Carbon::now()->startOfMonth();

        $first_employee = Employee::firstOrCreate(
            ['ci' => '21378987'], 
            [
                'num_employee'   => '100',
                'first_name' => 'Ana',
                'second_name' => 'Camila',
                'last_name' => 'Bello',
                'second_last_name' => 'Pérez',
                'birthdate' => '1994-05-11',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'M',
                'marital_status' => 'Soltero',
                'blood_type' => 'O+',
                'email' => 'ana.camila@gmail.com',
                'mobile_phone' => '04121234567',
                'home_phone' => '02863410565',
                'address' => 'PZO',
                'department_id' => 1,
                'position_id' => 1,
                'user_id' => null,
                'status' => false, // empleado prueba BAJA
                'use_meru_link' => false,
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
                'birthdate' => '1994-04-30',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'H',
                'marital_status' => 'Soltero',
                'blood_type' => 'O+',
                'email' => 'jose.bello@gmail.com',
                'mobile_phone' => '04121234567',
                'home_phone' => '02863410565',
                'address' => 'PZO',
                'department_id' => 1,
                'position_id' => 2,
                'user_id' => null,
                'status' => false, // empleado prueba vacations
                'use_meru_link' => false,
                'use_hid_card' => false,
                'use_locker' => false,
                'use_transport' => false,
            ]
        );

        Employee::firstOrCreate(
            ['ci' => '21378989'], 
            [
                'num_employee'   => '102',
                'first_name' => 'Riad',
                'second_name' => 'R',
                'last_name' => 'Abdo',
                'second_last_name' => 'A',
                'birthdate' => '1991-12-01',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'H',
                'marital_status' => 'Soltero',
                'blood_type' => 'O+',
                'email' => 'riad.abdo@gmail.com',
                'mobile_phone' => '04121234567',
                'home_phone' => '02863410565',
                'address' => 'PZO',
                'department_id' => 1,
                'position_id' => 2,
                'user_id' => 2,
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
                'first_name' => 'Supervisor',
                'second_name' => 'Perfil',
                'last_name' => 'Pruebas',
                'second_last_name' => 'Pruebas',
                'birthdate' => '1991-04-01',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'H',
                'marital_status' => 'Soltero',
                'blood_type' => 'O+',
                'email' => 'supervisor.pruebas@gmail.com',
                'mobile_phone' => '04121234567',
                'home_phone' => '02863410565',
                'address' => 'PZO',
                'department_id' => 1,
                'position_id' => 4,
                'user_id' => 3,
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
                'first_name' => 'Empleado',
                'second_name' => 'Perfil',
                'last_name' => 'Pruebas',
                'second_last_name' => 'Pruebas',
                'birthdate' => '1991-03-01',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'M',
                'marital_status' => 'Soltero',
                'blood_type' => 'O+',
                'email' => 'empleado.pruebas@gmail.com',
                'mobile_phone' => '04121234567',
                'home_phone' => '02863410565',
                'address' => 'PZO',
                'department_id' => 1,
                'position_id' => 5,
                'user_id' => 4,
                'status' => true,
                'use_meru_link' => true,
                'use_hid_card' => false,
                'use_locker' => false,
                'use_transport' => false,
            ]
        );


        Employee::firstOrCreate(
            ['ci' => '21373456'], 
            [
                'num_employee'   => '701',
                'first_name' => 'Carlos',
                'second_name' => 'Andres',
                'last_name' => 'Ramírez',
                'second_last_name' => 'Pérez',
                'birthdate' => '1994-04-30',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'H',
                'marital_status' => 'Soltero',
                'blood_type' => 'O+',
                'email' => 'carlos.ramirez@gmail.com',
                'mobile_phone' => '04121234567',
                'home_phone' => '02863410565',
                'address' => 'PZO',
                'department_id' => 3,
                'position_id' => 8,
                'user_id' => null,
                'status' => true,
                'use_meru_link' => false,
                'use_hid_card' => false,
                'use_locker' => false,
                'use_transport' => false,
            ]
        );

        Employee::firstOrCreate(
            ['ci' => '25373410'], 
            [
                'num_employee'   => '301',
                'first_name' => 'Carmen',
                'second_name' => 'Andrea',
                'last_name' => 'Ramírez',
                'second_last_name' => 'Pérez',
                'birthdate' => '1994-04-30',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'M',
                'marital_status' => 'Soltero',
                'blood_type' => 'O+',
                'email' => 'carmen.ramirez@gmail.com',
                'mobile_phone' => '04121234567',
                'home_phone' => '02863410565',
                'address' => 'PZO',
                'department_id' => 3,
                'position_id' => 8,
                'user_id' => null,
                'status' => true,
                'use_meru_link' => false,
                'use_hid_card' => false,
                'use_locker' => false,
                'use_transport' => false,
            ]
        );

        // Empleado para usuario guest (user_id = 5)
        Employee::firstOrCreate(
            ['ci' => '99999999'],
            [
                'num_employee'   => '999',
                'first_name'     => 'Invitado',
                'last_name'      => 'Pruebas',
                'birthdate'      => '2000-01-01',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality'    => 'V',
                'sex'            => 'H',
                'marital_status' => 'Soltero',
                'blood_type'     => 'O+',
                'email'          => 'invitado@sistema.com',
                'mobile_phone'   => '04121234567',
                'home_phone'     => '02863410565',
                'address'        => 'PZO',
                'department_id'  => 3,
                'position_id'    => 8,
                'user_id'        => 5,
                'status'         => true,
                'use_meru_link'  => true,
                'use_hid_card'   => false,
                'use_locker'     => false,
                'use_transport'  => false,
            ]
        );

        // Registros para seguridad
        Employee::firstOrCreate(
            ['ci' => '25373411'], 
            [
                'num_employee'   => '703',
                'first_name' => 'ALCALÁ',
                'second_name' => 'SEGURIDAD',
                'last_name' => 'SEGURIDAD',
                'second_last_name' => 'SEGURIDAD',
                'birthdate' => '1994-04-30',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'H',
                'marital_status' => 'Soltero',
                'blood_type' => 'O+',
                'email' => 'alcala.fake@gmail.com',
                'mobile_phone' => '04121234567',
                'home_phone' => '02863410565',
                'address' => 'PZO',
                'department_id' => 7,
                'position_id' => 7,
                'user_id' => null,
                'status' => true,
                'use_meru_link' => false,
                'use_hid_card' => false,
                'use_locker' => false,
                'use_transport' => false,
            ]
        );

        Employee::firstOrCreate(
            ['ci' => '25373412'], 
            [
                'num_employee'   => '704',
                'first_name' => 'GILBERTO',
                'second_name' => 'SEGURIDAD',
                'last_name' => 'SEGURIDAD',
                'second_last_name' => 'SEGURIDAD',
                'birthdate' => '1994-04-30',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'H',
                'marital_status' => 'Soltero',
                'blood_type' => 'O+',
                'email' => 'gilberto.fake@gmail.com',
                'mobile_phone' => '04121234567',
                'home_phone' => '02863410565',
                'address' => 'PZO',
                'department_id' => 7,
                'position_id' => 7,
                'user_id' => null,
                'status' => true,
                'use_meru_link' => false,
                'use_hid_card' => false,
                'use_locker' => false,
                'use_transport' => false,
            ]
        );

        Employee::firstOrCreate(
            ['ci' => '25373413'], 
            [
                'num_employee'   => '705',
                'first_name' => 'CRUZ',
                'second_name' => 'SEGURIDAD',
                'last_name' => 'SEGURIDAD',
                'second_last_name' => 'SEGURIDAD',
                'birthdate' => '1994-04-30',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'H',
                'marital_status' => 'Soltero',
                'blood_type' => 'O+',
                'email' => 'cruz.fake@gmail.com',
                'mobile_phone' => '04121234567',
                'home_phone' => '02863410565',
                'address' => 'PZO',
                'department_id' => 7,
                'position_id' => 7,
                'user_id' => null,
                'status' => true,
                'use_meru_link' => false,
                'use_hid_card' => false,
                'use_locker' => false,
                'use_transport' => false,
            ]
        );

        Employee::firstOrCreate(
            ['ci' => '25373414'], 
            [
                'num_employee'   => '706',
                'first_name' => 'SAUL',
                'second_name' => 'SEGURIDAD',
                'last_name' => 'SEGURIDAD',
                'second_last_name' => 'SEGURIDAD',
                'birthdate' => '1994-04-30',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'H',
                'marital_status' => 'Soltero',
                'blood_type' => 'O+',
                'email' => 'saul.fake@gmail.com',
                'mobile_phone' => '04121234567',
                'home_phone' => '02863410565',
                'address' => 'PZO',
                'department_id' => 7,
                'position_id' => 7,
                'user_id' => null,
                'status' => true,
                'use_meru_link' => false,
                'use_hid_card' => false,
                'use_locker' => false,
                'use_transport' => false,
            ]
        );

        Employee::firstOrCreate(
            ['ci' => '25373415'], 
            [
                'num_employee'   => '707',
                'first_name' => 'ANISETO',
                'second_name' => 'SEGURIDAD',
                'last_name' => 'SEGURIDAD',
                'second_last_name' => 'SEGURIDAD',
                'birthdate' => '1994-04-30',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'H',
                'marital_status' => 'Soltero',
                'blood_type' => 'O+',
                'email' => 'aniseto.fake@gmail.com',
                'mobile_phone' => '04121234567',
                'home_phone' => '02863410565',
                'address' => 'PZO',
                'department_id' => 7,
                'position_id' => 7,
                'user_id' => null,
                'status' => true,
                'use_meru_link' => false,
                'use_hid_card' => false,
                'use_locker' => false,
                'use_transport' => false,
            ]
        );

        Employee::firstOrCreate(
            ['ci' => '25373416'], 
            [
                'num_employee'   => '708',
                'first_name' => 'GERMAN',
                'second_name' => 'SEGURIDAD',
                'last_name' => 'PULIDO',
                'second_last_name' => 'SEGURIDAD',
                'birthdate' => '1994-04-30',
                'place_of_birth' => 'Puerto Ordaz',
                'nationality' => 'V',
                'sex' => 'H',
                'marital_status' => 'Soltero',
                'blood_type' => 'O+',
                'email' => 'german.fake@gmail.com',
                'mobile_phone' => '04121234567',
                'home_phone' => '02863410565',
                'address' => 'PZO',
                'department_id' => 7,
                'position_id' => 7,
                'user_id' => null,
                'status' => true,
                'use_meru_link' => false,
                'use_hid_card' => false,
                'use_locker' => false,
                'use_transport' => false,
            ]
        );
    }
}
