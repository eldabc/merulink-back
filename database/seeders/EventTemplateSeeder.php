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

            $clonEvent = $eventForTemplateOne->replicate();
            $clonEvent->start = null;
            $clonEvent->end = null;
            $clonEvent->save();

            EventTemplate::firstOrCreate(
                ['name' => 'Plantilla Uno'],
                [
                    'event_id' => $clonEvent->id,
                    'origin_event_id' => $eventForTemplateOne->id,
                ],
            );
        }

        $eventForTemplateTwo = Event::find(2);

        if ($eventForTemplateTwo) {
            
            $clonEvent = $eventForTemplateTwo->replicate();
            $clonEvent->start = null;
            $clonEvent->end = null;
            $clonEvent->save();
        
            EventTemplate::firstOrCreate(
                ['name' => 'Plantilla Dos'],
                [
                    'event_id' => $clonEvent->id,
                    'origin_event_id' => $eventForTemplateTwo->id,
                ],
            );
        }
    }
}
