<?php

namespace Database\Seeders;

use App\Models\SubDepartment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        SubDepartment::firstOrCreate(
            ['code' => 11],
            [
                'name' => 'Contabilidad',
                'department_id' => 1
            ]
        );

        SubDepartment::firstOrCreate(
            ['code' => 12],
            [
                'name' => 'Auditoría',
                'department_id' => 1
            ]
        );

        SubDepartment::firstOrCreate(
            ['code' => 13],
            [
                'name' => 'Cobranza',
                'department_id' => 1
            ]
        );

        SubDepartment::firstOrCreate(
            ['code' => 14],
            [   
                'name' => 'Tributos',
                'department_id' => 1
            ]
        );

        SubDepartment::firstOrCreate(
            ['code' => 15],
            [
                'name' => 'Compras',
                'department_id' => 1
            ]
        );

        SubDepartment::firstOrCreate(
            ['code' => 16],
            [
                'name' => 'Almacen',
                'department_id' => 1
            ]
        );

        SubDepartment::firstOrCreate(
            ['code' => 21],
            [   
                'name' => 'Front Desk',
                'department_id' => 2
            ]
        );

        SubDepartment::firstOrCreate(
            ['code' => 22],
            [
                'name' => 'Reserva',
                'department_id' => 2
            ]
        );

        SubDepartment::firstOrCreate(
            ['code' => 23],
            [
                'name' => 'Botones',
                'department_id' => 2
            ]
        );
    }
}
