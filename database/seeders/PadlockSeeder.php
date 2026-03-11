<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Padlock;

class PadlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Padlock::firstOrCreate(
            // Criterio de búsqueda
            ['serial' => '123456'], 
            [
             'pass'   => '11-32-66',
             'status' => 'Asignado',
            ]
        );

        Padlock::firstOrCreate(
            ['serial' => '123457'], 
            [
             'pass'   => '11-32-77',
             'status' => 'Disponible',
            ]
        );

        Padlock::firstOrCreate(
            ['serial' => '123458'], 
            [
             'pass'   => '11-32-88',
             'status' => 'Disponible',
            ]
        );

        Padlock::firstOrCreate(
            ['serial' => '123459'], 
            [
             'pass'   => '11-32-99',
             'status' => 'Disponible',
            ]
        );
    }
}
