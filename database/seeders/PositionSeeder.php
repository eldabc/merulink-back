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
    }
}
