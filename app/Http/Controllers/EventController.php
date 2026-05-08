<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventContact;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\BirthdayEventService;
use App\Services\EventTemplateService;
use App\Services\EventContactService;
use App\Services\GoogleCalendarService;
use App\Http\Resources\EventResource;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\BatchBankingEventRequest;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

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

    public function index(Request $request, GoogleCalendarService $googleCalendarService)
    {
        $categoryKeys = explode(',', $request->categoryKeys);

        $isAll = in_array('all', $categoryKeys);
        $includeBirthdays = $isAll || in_array('meru-birthdays', $categoryKeys);
        $includeEvents = $isAll || count(array_diff($categoryKeys, ['meru-birthdays'])) > 0;
        $includeGoogleEvents = in_array('google-calendar', $categoryKeys);

        $applyHistory = $request->boolean('history');
        $anyDateInCategory = $request->boolean('anyDateInCategory');
        $today = now();
        $year = $request->integer('year') ?: $today->year;
        $startOfYear = now()->setYear($year)->startOfYear();
        $endOfYear = now()->setYear($year)->endOfYear();
        $getAllYear = $isAll || $anyDateInCategory || ($year !== $today->year);    

        // colección base
        $events = collect();

        // EVENTOS NORMALES
        if ($includeEvents) {

            $query = Event::with(['eventCategory', 'location'])->onlyEventOrigin()->whereYear('start', $year);

            // Sino es all filtrar por categorías
            if (!$isAll) {
                $query->whereHas('eventCategory', function($q) use ($categoryKeys) {
                    $q->whereIn('key', $categoryKeys);
                });
            }

            if ($getAllYear) {
                $query->whereBetween('start', [$startOfYear, $endOfYear])
                    ->orderBy('start', 'desc');
            } elseif ($applyHistory) {
                $query->where('start', '<', $today)
                    ->orderBy('start', 'desc');
            } else {
                $query->where('start', '>=', $today)
                    ->orderBy('start', 'asc');
            }        

            $eventResults = EventResource::collection($query->get())->resolve();

            $events = $events->concat($eventResults);

            if ($includeGoogleEvents || $isAll) {

                // Google IDs en BD
                $internalStoreGoogleEvents = Event::query()
                    ->whereHas('eventCategory', function($q) {
                        $q->where('key', 'google-calendar');
                    })
                    ->whereYear('start', $year)
                    ->pluck('external_id')
                    ->filter()
                    ->flip();

                // eventos desde Google
                $googleEvents = collect($googleCalendarService->fetchHolidays($year));

                // filtrar duplicados y ordenar según flags
               $googleFilterEvents = $googleEvents
                    ->filter(function ($event) use ($internalStoreGoogleEvents) {

                        return !isset(
                            $internalStoreGoogleEvents[$event['id']]
                        );
                    })

                    // filtro fecha
                    ->filter(function ($event) use ( $getAllYear, $applyHistory, $today, $startOfYear, $endOfYear ) {

                        $eventDate = \Carbon\Carbon::parse($event['start']);

                        // traer TODO el año
                        if ($getAllYear) {
                            return $eventDate->between(
                                $startOfYear,
                                $endOfYear
                            );
                        }

                        // historial
                        if ($applyHistory) {
                            return $eventDate->lt($today);
                        }

                        // futuros
                        return $eventDate->gte($today);
                    })

                    // ordenar
                    ->sortBy(function ($event) use ($applyHistory) {

                        return \Carbon\Carbon::parse($event['start'])->timestamp;

                    }, SORT_REGULAR, $applyHistory) // reverse si history
                    ->values();

                $events = $events->concat($googleFilterEvents);
            }
        }

        // CUMPLEAÑOS
        if ($includeBirthdays) {
            $events = $events->concat($this->birthdayEventService->calculateBirthdayEvents($applyHistory, $year, $isAll));
        }

        return response()->json([ 'data' => $events->values()->all() ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request, EventTemplateService $templateService, EventContactService $eventContactService)
    {
        $data = $request->validated();
        return DB::transaction(function () use ($data, $templateService, $eventContactService) {

            $data['event_category_id'] = EventCategory::where('key', $data['category_key'])->value('id');
            // return response()->json([ 'data' => $data ]);
            $event = Event::create($data);

            if (filled($data['template_name']) && !$event->templateOrigin()->exists()) {
                $templateService->createFromEvent($event, $data['template_name']);
            }

            if(filled($data['contacts'])) {
                $eventContactService->syncContacts($event, $data['contacts']);
            }


            return new EventResource($event->load([
                'eventCategory',
                // 'templateOrigin', 
                'location',
                'contacts.phones'
            ]));
        });

    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return new EventResource($event->load([
            'eventCategory', 
            'location', 
            'templateOrigin',
            'contacts.phones'
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreEventRequest $request, Event $event, EventTemplateService $templateService, EventContactService $eventContactService)
    {
        $data = $request->validated();
        return DB::transaction(function () use ($data, $event, $templateService, $eventContactService) {

            $data['event_category_id'] = EventCategory::where('key', $data['category_key'])->value('id');

            $event->update($data);

            if (filled($data['template_name']) && !$event->templateOrigin()->exists()) {
                $templateService->createFromEvent($event, $data['template_name']);
            }

            if(filled($data['contacts'])) {
                $eventContactService->syncContacts($event, $data['contacts']);
            }

            return new EventResource($event->load([
                'eventCategory',
                'location',
                'templateOrigin',
                'contacts.phones'
            ]));
        });

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        if ($event->status->isConfirmed()) {
            return response()->json([
                'message' => 'No se puede eliminar un evento que está confirmado.'
            ], 422);
        }

        $event->delete();

        return response()->json([
            'message' => "El evento {$event->title} ha sido eliminado correctamente."
        ], 200);
    }

    public function batchBanking(BatchBankingEventRequest $request) 
    {   
        $data = $request->validated(); // Array de objetos

        return DB::transaction(function () use ($data) {
            
            // En este caso los eventos siempre tienen la misma categoría
            $eventCategoryId = EventCategory::where('key', $data[0]['category_key'])->value('id');

            if ($eventCategoryId) Event::where('event_category_id', $eventCategoryId)->delete();

            $createdEvents = new EloquentCollection;
            foreach ($data as $eventData) {
                $eventData['event_category_id'] = $eventCategoryId;
                $event = Event::create($eventData);
                $createdEvents->push($event);
            }

            return EventResource::collection($createdEvents->load(['eventCategory']));
        });
    }
}
