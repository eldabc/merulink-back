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

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::get();
        return response()->json([ 'data' => $locations ]);
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
    public function show(Location $location)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        //
    }
}
