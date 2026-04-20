<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventForTemplateOne = Event::find(1);

        if ($eventForTemplateOne) {

            $nuevoEvento = $eventForTemplateOne->replicate();
            $nuevoEvento->save();

            EventTemplate::firstOrCreate(
                ['name' => 'Plantilla Uno'],
                [
                    'event_id' => $nuevoEvento->id,
                    'origin_event_id' => $eventForTemplateOne->id,
                ],
            );
        }

        $eventForTemplateTwo = Event::find(2);

        if ($eventForTemplateTwo) {
            
            $nuevoEvento = $eventForTemplateTwo->replicate();
            $nuevoEvento->save();
        
            EventTemplate::firstOrCreate(
                ['name' => 'Plantilla Dos'],
                [
                    'event_id' => $nuevoEvento->id,
                    'origin_event_id' => $eventForTemplateTwo->id,
                ],
            );
        }
    }
}
