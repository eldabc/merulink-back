<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\BirthdayEventService;
use App\Http\Resources\EventResource;
use App\Http\Requests\StoreEventRequest;

class EventController extends Controller
{
    protected $birthdayEventService;

    public function __construct(BirthdayEventService $birthdayEventService)
    {
        $this->birthdayEventService = $birthdayEventService;
    }

    /**
     * Display a listing of the resource.
     */
    
    // use Illuminate\Support\Facades\DB;

    public function index(Request $request)
    {
        $categoryKeys = explode(',', $request->categoryKeys);

        $includeAll = in_array('all', $categoryKeys);
        $includeBirthdays = $includeAll || in_array('meru-birthdays', $categoryKeys);
        $includeEvents = $includeAll || count(array_diff($categoryKeys, ['meru-birthdays'])) > 0;

        // history SOLO aplica si NO es "all"
        $applyHistory = !$includeAll && $request->boolean('history');

        // colección base
        $events = collect();

        // EVENTOS NORMALES
        if ($includeEvents) {

            $query = Event::with(['eventCategory', 'location']);

            // Sino es all filtrar por categorías
            if (!$includeAll && !empty($categoryKeys)) {
                $query->whereHas('eventCategory', function($q) use ($categoryKeys) {
                    $q->whereIn('key', $categoryKeys);
                });
            }

            if ($request->boolean('history')) {
                $query->where('start', '<', now())
                    ->orderBy('start', 'desc');
            } elseif (!$includeAll) { // si es "all", no filtramos fechas
                $query->where('start', '>=', now())
                    ->orderBy('start', 'asc');
            }
            

            $eventResults = EventResource::collection($query->get())->resolve();
            // return response()->json([ 'PRINT' => $query->get() ]);

            $events = $events->concat($eventResults);
        }

        // CUMPLEAÑOS
        if ($includeBirthdays) {
            $events = $events->concat($this->birthdayEventService->calculateBirthdayEvents($request->boolean('history')));
        }

        return response()->json([ 'data' => $events->values()->all() ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();
        return DB::transaction(function () use ($data) {

            // $key = $request->input('extended_props.0.category_key');
            $data['event_category_id'] = EventCategory::where('key', $data['extended_props']['category_key'])->value('id');
            return response()->json([ 'data' => $data ]);

            $event = Event::create($data);

            if (filled($data['template_name'])) {
                $eventTemplate = EventTemplate::create([
                    'name' => $data['template_name'],
                    'event_id' => $event->id,
                ]);
            }

            // if (isset($data['contacts'])) {
            //     foreach ($data['contacts'] as $contact) {
            //         $event->emergencyContacts()->create($contact);
            //     }
            // }

            return new EventResource($event->load([
                'eventCategory',
                'eventTemplate', 
                'location'
            ]));
        });

    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        //
    }
}
