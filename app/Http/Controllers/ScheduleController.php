<?php

/**
 * ---------------------------------------------------------------------------
 * MeruLink — Sistema de gestión hotelera
 * ---------------------------------------------------------------------------
 * Copyright (c) 2026 Hotel Plaza Meru. Todos los derechos reservados.
 *
 * Este software es propiedad exclusiva de Hotel Plaza Meru. Queda prohibida
 * su reproducción, distribución, comunicación pública, modificación o uso
 * total o parcial sin la autorización previa y por escrito del titular.
 * ---------------------------------------------------------------------------
 */

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Resources\ScheduleResource;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
        // $query = Schedule::with(['employee']); 
        
    //     // Filtro
    //     if ($request->filled('start') && $request->filled('end') && $request->filled('departmentId')) {
    //         $query->whereBetween('date', [$request->start, $request->end]);
    //     }

    //     $query->orderBy('date', 'asc');
    //     $schedules = $query->get();

    //     return ScheduleResource::collection($schedules);
    // }

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
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        //
    }
}
