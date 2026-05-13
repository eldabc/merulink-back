<?php

namespace App\Http\Controllers;
  
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventContact;

use Illuminate\Http\Request;
use App\Support\EventDateRange;
use Illuminate\Support\Facades\DB;
use App\Services\BirthdayEventService;
use App\Services\EventTemplateService;
use App\Services\EventContactService;
use App\Services\GoogleCalendarEventService;

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

    public function index(Request $request, GoogleCalendarEventService $googleCalendarEventService)
    {
        $categoryKeys = explode(',', $request->categoryKeys);
        $applyHistory = $request->boolean('history');
        $anyDateInCategory = $request->boolean('anyDateInCategory');

        $getAllCategories = in_array('all', $categoryKeys);
        $includeBirthdays = $getAllCategories || in_array('meru-birthdays', $categoryKeys);
        $includeGoogleEvents = in_array('google-calendar', $categoryKeys);
        $includeRegularEvents = $getAllCategories || count(array_diff($categoryKeys, ['meru-birthdays'])) > 0;     

        $today = now();
        $year = $request->integer('year') ?: $today->year;
        $month = $request->filled('month') ? $request->integer('month') : null;
        $startOfYear = now()->setYear($year)->startOfYear();
        $endOfYear = now()->setYear($year)->endOfYear(); 
        $getAllYear = $getAllCategories || $anyDateInCategory;
        
        [$startDate, $endDate] = EventDateRange::resolve(
            $year,
            $month,
            $getAllYear,
            $applyHistory,
            $today
        );

        // colección base
        $events = collect();

        // Eventos Regulares
        if ($includeRegularEvents) {

            $query = Event::with(['eventCategory', 'location'])->onlyEventOrigin();

            // Filtrar por categorías
            if (!$getAllCategories) {
                $query->whereHas('eventCategory', function($q) use ($categoryKeys) {
                    $q->whereIn('key', $categoryKeys);
                });
            }

            if ($month || $getAllYear) { 
                $query->whereBetween('start', [$startDate, $endDate]);
            } elseif ($applyHistory) {

                if ($year !== $today->year) { // historial de años anteriores
                    $query->whereBetween('start', [$startOfYear, $endOfYear]);
                } else {
                    $query->whereBetween('start', [$startOfYear, $today]);
                }

            } else { // flujo por default
                $query->where('start', '>=', $startDate);
            }
     
            $query->orderBy('start', $applyHistory ? 'desc' : 'asc'); 

            $eventResults = EventResource::collection($query->get())->resolve();
            $events = $events->concat($eventResults);

            if ($includeGoogleEvents || $getAllCategories) {

                $googleEvents = $googleCalendarEventService
                    ->getFilteredEvents(
                        $year,
                        $startDate,
                        $endDate,
                        $applyHistory,
                        $today
                    );
                $events = $events->concat($googleEvents);

            }
        }

        // Cumpleaños
        if ($includeBirthdays) {
            $events = $events->concat($this->birthdayEventService->calculateBirthdayEvents($startDate, $endDate, $year, $applyHistory));
        }

        // agrupar y ordenar cronológicamente ve-holidays y google-calendar events
        if (in_array('ve-holidays', $categoryKeys)) {
            $events = $events
                ->sortBy(function ($event) {
                    return \Carbon\Carbon::parse(
                        $event['start']
                    )->timestamp;

                }, SORT_REGULAR, $applyHistory)
                ->values();

            return response()->json([
                'data' => $events->all()
            ]);
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
