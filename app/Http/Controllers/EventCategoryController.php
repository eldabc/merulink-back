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

use App\Models\EventCategory;
use Illuminate\Http\Request;

class EventCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $eventCategory = EventCategory::get();
        return response()->json([ 'data' => $eventCategory ]);
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
    public function show(EventCategory $eventCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventCategory $eventCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventCategory $eventCategory)
    {
        //
    }
}
