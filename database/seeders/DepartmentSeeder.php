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
            ['id' => 1],
            [
             'code' => 1,
             'name' => 'Administración',
            ]
        );

        Department::firstOrCreate(
            ['id' => 2],
            [ 
              'code' => 2,
              'name' => 'Recepción',
            ]  
        );
    }
}
