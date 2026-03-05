<?php

namespace App\Http\Controllers;

use App\Models\Locker;
use Illuminate\Http\Request;
use App\Http\Resources\LockerResource;
use App\Http\Requests\StoreLockerRequest;

class LockerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Usamos 'with' para evitar el problema de N+1 consultas
        $lockers = Locker::with('lockerCategory')->get();
        return LockerResource::collection($lockers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLockerRequest $request)
    {
        $data = $request->validated();

        // Crear
        $locker = Locker::create([
            'code'               => $data['code'],
            'status'             => $data['status'],
            'locker_category_id' => $data['category']['id'], 
        ]);

        return new LockerResource($locker->load('lockerCategory'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Locker $locker)
    {
        return "Show";
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Locker $locker)
    {
        return "Update";
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Locker $locker)
    {
        return "Destroy";
    }
}
