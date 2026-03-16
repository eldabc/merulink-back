<?php

namespace Database\Seeders;

use App\Models\PadlockPattern;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PadlockPatternSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PadlockPattern::firstOrCreate(
            ['model_name' => 'Modelo Base'], 
            [
             'reset_instructions' => 'Hacer un mínimo de dos giros en cualquier dirección para reiniciar.',
             'unlock_sequence' => [
                [
                    'action' => 'girar',
                    'amount' => 1,
                    'direction' => 'derecha'
                ],
                [
                    'action' => 'girar',
                    'amount' => 2,
                    'direction' => 'izquierda'
                ],
                [
                    'action' => 'girar',
                    'amount' => 1,
                    'direction' => 'derecha'
                ]
             ]
            ]
        );
    }
}
