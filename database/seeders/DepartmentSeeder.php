<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Department::firstOrCreate(
            ['code' => 1],
            ['name' => 'Administración']
        );

        Department::firstOrCreate(
            ['code' => 2],
            ['name' => 'Recepción']  
        );

        Department::firstOrCreate(
            ['code' => 3],
            ['name' => 'Ama de Llaves']  
        );

        Department::firstOrCreate(
            ['code' => 4],
            ['name' => 'Alimentos y Bebidas']  
        );

        Department::firstOrCreate(
            ['code' => 5],
            ['name' => 'Ventas']  
        );

        Department::firstOrCreate(
            ['code' => 6],
            ['name' => 'RRHH']  
        );

        Department::firstOrCreate(
            ['code' => 7],
            ['name' => 'Seguridad']  
        );

        Department::firstOrCreate(
            ['code' => 8],
            ['name' => 'Mantenimiento']  
        );

        Department::firstOrCreate(
            ['code' => 9],
            ['name' => 'Sistemas']  
        );

        Department::firstOrCreate(
            ['code' => 10],
            ['name' => 'Externo']  
        );
    }
}
