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
        $padlock = Padlock::firstOrCreate(
            // Criterio de búsqueda
            ['serial' => '101-102'], 
            [
                'pass'   => '11-32-77',
                'status' => 'Disponible',
            ]
        );
    }
}
