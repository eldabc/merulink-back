<?php

namespace App\Http\Controllers;

use App\Models\Locker;
use Illuminate\Http\Request;
use App\Http\Resources\LockerResource;

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
    public function store(Request $request)
    {
        // Validar
        $validated = $request->validate([
            'code'                => 'required|string|unique:lockers,code',
            'status'              => 'required|string',
            'category.id'  => 'required|exists:locker_categories,id',
        ]);

        // Crear
        $locker = Locker::create([
            'code'               => $validated['code'],
            'status'             => $validated['status'],
            'locker_category_id' => $validated['category']['id'], 
        ]);

        // Carga la relación
        $locker->load('lockerCategory');

        return new LockerResource($locker);
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
