<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Services\RecurringEventService;

class GenerateRecurringEvents extends Command
{
    protected $signature =
        'events:generate-recurring';

    protected $description =
        'Generar eventos recurrentes';

    public function handle(RecurringEventService $service): void {

        $events = Event::query()
            ->where('repeat_event', true)
            ->whereNull('parent_event_id')
            ->get();

            
        foreach ($events as $event) {
            $service->generateNextOccurrence(
                $event
            );
        }

        $this->info(
            'Eventos recurrentes generados.'
        );
    }
}