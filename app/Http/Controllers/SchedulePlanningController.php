<?php

namespace App\Http\Controllers;

use App\Models\SchedulePlanning;
use Illuminate\Http\Request;
use App\Http\Resources\SchedulePlanningResource;


class SchedulePlanningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SchedulePlanning::with(['department', 'schedules']); 
        
        // Filtro
        if ($request->filled('start') && $request->filled('end') && $request->filled('departmentId')) {
            $query->where('department_id', $request->departmentId);
            $query->where('start', $request->start);
            $query->where('end', $request->end);
        }

        // $query->orderBy('date', 'asc');
        $schedules = $query->get();

        return SchedulePlanningResource::collection($schedules);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(SchedulePlanning $schedulePlanning)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SchedulePlanning $schedulePlanning)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SchedulePlanning $schedulePlanning)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchedulePlanning $schedulePlanning)
    {
        //
    }
}
