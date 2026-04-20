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

        Event::firstOrCreate(
            [ 'title' => 'Evento 1 meru-events' ],
            [
                'start' => now()->addHours(80)->format('Y-m-d\TH:i:s'),
                'end' => now()->addHours(82)->format('Y-m-d\TH:i:s'),
                'extended_props' => [
                    'status' => 'Tentativo',
                    'repeat_event' => true,
                    'repeat_interval' => 'Mensual',
                    'create_alert' => true,
                    'coloring_day' => true,
                    'description' => 'Descripción del evento 1',
                    'comments' => 'Comentarios del evento 1',
                    'created_by' => "Ana Luna"
                ],
                'event_category_id' => 1,
                'location_id' => 1
            ]
        );

        Event::firstOrCreate(
            [ 'title' => 'Evento 2 meru-events' ],
            [
                'start' => now()->addHours(78)->format('Y-m-d\TH:i:s'),
                'end' => now()->addHours(80)->format('Y-m-d\TH:i:s'),
                'extended_props' => [
                    'status' => 'Tentativo',
                    'repeat_event' => true,
                    'repeat_interval' => 'Mensual',
                    'create_alert' => true,
                    'coloring_day' => true,
                    'description' => 'Descripción del evento 2',
                    'comments' => 'Comentarios del evento 2',
                    'created_by' => "Ana Luna"
                ],
                'event_category_id' => 1,
                'location_id' => 2
            ]
        );


        Event::firstOrCreate(
            [ 'title' => 'Evento 3 meru-events' ],
            [
                'start' => '2026-01-30T20:00:00',
                'end' => '2026-01-30T23:00:00',
                'extended_props' => [
                    'status' => 'Tentativo',
                    'repeat_event' => true,
                    'repeat_interval' => 'Mensual',
                    'create_alert' => true,
                    'coloring_day' => true,
                    'description' => 'Descripción del evento 3',
                    'comments' => 'Comentarios del evento 3',
                    'created_by' => "Ana Luna"
                ],
                'event_category_id' => 1,
                'location_id' => 3
            ]
        );

        Event::firstOrCreate(
            [ 'title' => 'Evento 4 wedding-nights' ],
            [
                'start' => now()->addHours(82)->format('Y-m-d\TH:i:s'),
                'end' => now()->addHours(84)->format('Y-m-d\TH:i:s'),
                'extended_props' => [
                    'status' => 'Tentativo',
                    'coloring_day' => true,
                    'comments' => 'Comentario del evento 4',
                    'created_by' => "Ana Luna"
                ],
                'event_category_id' => 2,
                'location_id' => 2
            ]
        );


         Event::firstOrCreate(
            [ 'title' => 'Evento 5 dinner-heights' ],
            [
                'start' => '2026-05-30T20:00:00',
                'end' => '2026-05-30T23:00:00',
                'extended_props' => [
                    'status' => 'Tentativo',
                    'repeat_event' => true,
                    'repeat_interval' => 'Mensual',
                    'create_alert' => true,
                    'coloring_day' => true,
                    'description' => 'Descripción del evento 5',
                    'comments' => 'Comentarios del evento 5',
                    'created_by' => "Ana Luna"
                ],
                'event_category_id' => 3,
                'location_id' => 3
            ]
        );

         Event::firstOrCreate(
            [ 'title' => 'Evento 6 ve-holidays' ],
            [
                'start' => '2026-12-30T20:00:00',
                'end' => '2026-12-30T23:00:00',
                'extended_props' => [
                    'status' => 'Tentativo',
                    'repeat_event' => true,
                    'repeat_interval' => 'Mensual',
                    'create_alert' => true,
                    'coloring_day' => true,
                    'description' => 'Descripción del evento 6',
                    'comments' => 'Comentarios del evento 6',
                    'created_by' => "Ana Luna"
                ],
                'event_category_id' => 4,
            ]
        );


        Event::firstOrCreate(
            [ 'title' => 'Evento 7 executive-mod' ],
            [
                'start' => now()->addHours(86)->format('Y-m-d\TH:i:s'),
                'end' => now()->addHours(90)->format('Y-m-d\TH:i:s'),
                'extended_props' => [
                    'status' => 'Tentativo',
                    'coloring_day' => true,
                    'comments' => 'Comentario del evento 7',
                    'created_by' => "Riad Abdo"
                ],
                'event_category_id' => 7,
                'location_id' => null
            ]
        );

        
        Event::firstOrCreate(
            [ 'title' => 'Evento 8 banking-mondays' ],
            [
                'start' => '2026-12-30T20:00:00',
                'end' => '2026-12-30T23:00:00',
                'extended_props' => [
                    'status' => 'Tentativo',
                    'repeat_event' => true,
                    'repeat_interval' => 'Mensual',
                    'create_alert' => true,
                    'coloring_day' => true,
                    'description' => 'Descripción del evento 8',
                    'comments' => 'Comentarios del evento 8',
                    'created_by' => "Ana Luna"
                ],
                'event_category_id' => 8,
            ]
        );
    }
}
