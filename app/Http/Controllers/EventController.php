<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Employee;
use App\Models\EventCategory;
use App\Http\Resources\EventResource;
use App\Http\Resources\BirthdayEventResource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
// use Illuminate\Support\Facades\DB;

public function index(Request $request)
{
    $query = Event::with(['eventCategory', 'location']);
    $categoryKeysArray = explode(',', $request->categoryKeys );

    if ($request->filled('categoryKeys') ) {
        if( $request->categoryKeys === 'meru-birthdays') {

            $today = Carbon::today();

            $employees = Employee::with('department')
                            ->whereNotNull('birthdate')
                            ->get()
                            ->filter(function ($employee) use ($today) {
                                $birthdayThisYear = Carbon::parse($employee->birthdate)
                                    ->year($today->year);

                                return $birthdayThisYear->greaterThanOrEqualTo($today);
                            })
                            ->sortBy(function ($employee) use ($today) {
                                return Carbon::parse($employee->birthdate)
                                    ->year($today->year);
                            })
                            ->values();

            $eventCategory = EventCategory::where('key', 'meru-birthdays')->first();
            $events = $employees->map(function ($employee) use ($today, $eventCategory) {
                return (new BirthdayEventResource($employee, $today, $eventCategory))->resolve();
            });

            // return response()->json([ 'data' => $eventCategory ]);
            return response()->json([ 'data' => $events ]);
        }

        // Flujo usual events
        $query->whereHas('eventCategory', function($q) use ($categoryKeysArray) {
            $q->whereIn('key', $categoryKeysArray);
        });
    }

    if ($request->filled('history')) {
        $query->where('start', '<', now())
              ->orderBy('start', 'desc');
    } else {
        $query->where('start', '>=', now())
              ->orderBy('start', 'asc');
    }

    return EventResource::collection($query->get());
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
