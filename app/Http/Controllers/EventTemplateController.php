<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventTemplate;

use Illuminate\Http\Request;
use App\Http\Resources\EventTemplateResource;

class EventTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EventTemplate::with(['event.eventCategory', 'event.location']);

        if ($request->filled('selectedCategory')) {
            $query->whereHas('event.eventCategory', function($q) use ($request) {
                $q->where('key', $request->selectedCategory);
            });
        }
        
        $templates = $query->get();

        return EventTemplateResource::collection($templates);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EventTemplate $eventTemplate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventTemplate $eventTemplate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventTemplate $eventTemplate)
    {
        $eventId = $eventTemplate->event_id;

        // Al borrar el evento, la plantilla se borraría sola por el cascade de la migración,
        // pero para evitar conflictos de "record not found", borramos el evento directamente.
        Event::where('id', $eventId)->delete();
        $eventTemplate->delete();

        return response()->json([
            'message' => "La plantilla {$eventTemplate->title} ha sido eliminada correctamente."
        ], 200);
    }
}
