<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Position::firstOrCreate(
            ['code' => '110'], 
            [
                'name'   => 'Gerente General',
                'department_id' => 1,
                'sub_department_id' => 1
            ]
        );

        Position::firstOrCreate(
            ['code' => '111'], 
            [
                'name'   => 'Contralor',
                'department_id' => 1,
                'sub_department_id' => 1
            ]
        );

        
        Position::firstOrCreate(
            ['code' => '112'], 
            [
                'name'   => 'Contador',
                'department_id' => 1,
                'sub_department_id' => 1
            ]
        );

        Position::firstOrCreate(
            ['code' => '113'], 
            [
                'name'   => 'Administrador',
                'department_id' => 1,
                'sub_department_id' => 1
            ]
        );

        Position::firstOrCreate(
            ['code' => '114'], 
            [
                'name'   => 'Analista',
                'department_id' => 1,
                'sub_department_id' => 1
            ]
        );

        Position::firstOrCreate(
            ['code' => '115'], 
            [
                'name'   => 'Ejecutiva',
                'department_id' => 1,
                'sub_department_id' => 1
            ]
        );

        Position::firstOrCreate(
            ['code' => '711'], 
            [
                'name'   => 'Oficial de Seguridad',
                'department_id' => 7,
                'sub_department_id' => null
            ]
        );

        Position::firstOrCreate(
            ['code' => '300'], 
            [
                'name'   => 'Ama de Llaves',
                'department_id' => 3,
                'sub_department_id' => null
            ]
        );
    }
}
