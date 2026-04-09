<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Event::with(['eventCategory', 'location']);
        if ($request->filled('categoryKeys')) {
            
            if ($request->categoryKeys[0] === 'meru-birthdays') {
                $query->whereHas('eventCategory', function($q) {
                    $q->where('key', 'meru-birthdays');
                })->with('department');
            } else {
                $categoryKeysArray = explode(',', $request->categoryKeys);
                $query->whereHas('eventCategory', function($q) use ($categoryKeysArray) {
                    $q->whereIn('key', $categoryKeysArray);
                });
            }
        }

        if ($request->filled('history')) {
            $query->where('start', '<', now())
                  ->orderBy('start', 'desc');;
        } else {
            $query->where('start', '>=', now())
                  ->orderBy('start', 'asc');
        }
            // return response()->json([ 'data' => $query->get() ]);
        
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
