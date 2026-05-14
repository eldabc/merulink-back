<?php

namespace App\Services;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RecurringEventService
{
    public function generateNextOccurrence(Event $event): void
    {
        try {

            /*
            | Busca Primera/Última ocurrencia existente
            */
            $lastOccurrence = Event::query()
                ->where(function ($q) use ($event) {

                    $q->where('id', $event->id)
                    ->orWhere(
                        'parent_event_id',
                        $event->id
                    );
                })
                ->latest('start')
                ->first();

            if (!$lastOccurrence) {
                return;
            }

            // Evitar duplicados
            if (Carbon::parse($lastOccurrence->start)->isFuture()) {
                return;
            }

            
            // Clonar última ocurrencia
            $nextStart = Carbon::parse($lastOccurrence->start);
            $nextEnd = Carbon::parse($lastOccurrence->end);


            // Aplicar intervalo
            switch ($event->repeat_interval) {

                case 'WEEKLY':

                    $nextStart->addWeek();
                    $nextEnd->addWeek();
                    break;

                case 'WEEKLY_2':

                    $nextStart->addWeeks(2);
                    $nextEnd->addWeeks(2);
                    break;

                case 'MONTHLY':

                    $nextStart->addMonth();
                    $nextEnd->addMonth();
                    break;

                case 'YEARLY':

                    $nextStart->addYear();
                    $nextEnd->addYear();
                    break;

                default:
                    Log::channel('recurring_events')
                        ->warning('Inválido repeat_interval', [
                            'event_id' => $event->id,
                            'repeat_interval' => $event->repeat_interval,
                        ]);
                    return;
            }


            // Hasta cuando generar recurrentes
            if (
                empty(!$event->repeat_until) && $nextStart->gt($event?->repeat_until) ||
                !$event->repeat_always ||
                !$event->is_repeat_active ||
                $event->templateRecord()->exists()
            ) {
                return;
            }

            // Fechas originales del evento raíz
            $originalStart = Carbon::parse($event->start);
            $originalEnd = Carbon::parse($event->end);


            //Mantener hora original del evento raíz
            $nextStart->setTime(
                $originalStart->hour,
                $originalStart->minute,
                $originalStart->second
            );

            $nextEnd->setTime(
                $originalEnd->hour,
                $originalEnd->minute,
                $originalEnd->second
            );


            /*
            | Crear nueva ocurrencia
            */
            $newEvent = Event::create([

                'title' => $event->title. " automátización",
                'start' => $nextStart,
                'end' => $nextEnd,
                'all_day' => $event->all_day,
                'external_source' => $event->external_source,           
                'external_id' => $event->external_id,          
                'repeat_event' => false,
                'repeat_interval' => null,
                'parent_event_id' => $event->id,           
                'extended_props' => $event->extended_props,
                'event_category_id' => $event->event_category_id,           
                'location_id' => $event->location_id,

            ]);

            Log::channel('recurring_events')
                ->info('Evento Recurrente creado', [
                    'parent_event_id' => $event->id,
                    'new_event_id' => $newEvent->id,
                    'repeat_interval' => $event->repeat_interval,
                    'start' => $nextStart->toDateTimeString(),
                    'end' => $nextEnd->toDateTimeString(),
                ]);

        } catch (\Throwable $e) {

            Log::channel('recurring_events')
                ->error('Generación eventos recurrentes falló', [
                    'event_id' => $event->id ?? null,
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
        }
    }
}