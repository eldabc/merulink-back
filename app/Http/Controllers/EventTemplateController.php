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
    public function index()
    {
        $templates = EventTemplate::with(['event.eventCategory', 'event.location'])->get();
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
        //
    }
}
