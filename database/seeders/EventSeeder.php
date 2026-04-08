<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fechaConHora = now()->setTime(13, 0, 0)->format('Y-m-d\TH:i:s');
        // Resultado: "2026-04-07T13:00:00"

        Event::firstOrCreate(
            [ 'title' => 'Evento 1 meru-events' ],
            [
                'start' => now()->addHours(78)->format('Y-m-d\TH:i:s'),
                'end' => now()->addHours(80)->format('Y-m-d\TH:i:s'),
                'extended_props' => [
                    // category: 'meru-events',
                    // label: 'Eventos Merú',
                    'status' => 'Tentativo',
                    // 'locationId' => 1,
                    // 'locationName' => 'Salon 1',
                    'repeatEvent' => true,
                    'repeatInterval' => 'Mensual',
                    'createAlert' => true,
                    'coloringDay' => true,
                    'description' => 'Descripción del evento 1',
                    'comments' => 'Comentarios del evento 1',
                    'createdBy' => "Ana Luna"
                ],
                'event_category_id' => 1,
                'location_id' => 1
            ]
        );

        Event::firstOrCreate(
            [ 'title' => 'Evento 2 wedding-nights' ],
            [
                'start' => now()->addHours(82)->format('Y-m-d\TH:i:s'),
                'end' => now()->addHours(84)->format('Y-m-d\TH:i:s'),
                'extended_props' => [
                    // 'category' => 'wedding-nights',
                    // 'label' => 'Plan Noche de Bodas',
                    'status' => 'Tentativo',
                    // 'locationId' => 2,
                    // 'locationName' => 'Salon 2',
                    'coloringDay' => true,
                    'comments' => 'Comentario del evento 2',
                    'createdBy' => "Ana Luna"
                ],
                'event_category_id' => 2,
                'location_id' => 2
            ]
        );

        Event::firstOrCreate(
            [ 'title' => 'Evento 3 executive-mod' ],
            [
                'start' => now()->addHours(86)->format('Y-m-d\TH:i:s'),
                'end' => now()->addHours(90)->format('Y-m-d\TH:i:s'),
                'extended_props' => [
                    // 'category' => 'executive-mod',
                    // 'label' => 'Ejecutivos MOD',
                    'status' => 'Tentativo',
                    'coloringDay' => true,
                    'comments' => 'Comentario del evento 4',
                    'createdBy' => "Riad Abdo"
                ],
                'event_category_id' => 7,
                'location_id' => null
            ]
        );
    }
}
