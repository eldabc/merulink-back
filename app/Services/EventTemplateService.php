<?php
    
namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\DB;

class EventTemplateService
{
    /**
     * Clona un evento y lo registra como una nueva plantilla.
     */
    public function createFromEvent(Event $event, string $templateName): Event
    {
        return DB::transaction(function () use ($event, $templateName) {
            // Replicar el evento base
            $clonEvent = $event->replicate();
            
            // Limpiar fechas para la plantilla
            $clonEvent->start = null;
            $clonEvent->end = null;
            $clonEvent->save();

            // Registrar el origen de la plantilla
            $event->templateOrigin()->create([
                'name'     => $templateName,
                'event_id' => $clonEvent->id,
            ]);

            return $clonEvent;
        });
    }
}