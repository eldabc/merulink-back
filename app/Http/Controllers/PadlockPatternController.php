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

use App\Models\PadlockPattern;
use Illuminate\Http\Request;
use App\Http\Resources\PadlockPatternResource;
use App\Http\Requests\StorePadlockPatternRequest;

class PadlockPatternController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patterns = PadlockPattern::all();
        return PadlockPatternResource::collection($patterns);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePadlockPatternRequest $request)
    {
        $data = $request->validated();

        // Crear
        $patterns = PadlockPattern::create([
            'model_name'             => $data['modelName'],
            'reset_instructions'     => $data['resetInstructions'],
            'unlock_sequence'        => $data['unlockSequence'],
        ]);

        return new PadlockPatternResource($patterns);
    }

    /**
     * Display the specified resource.
     */
    public function show(PadlockPattern $padlockPattern)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePadlockPatternRequest $request, PadlockPattern $pattern)
    {
        $data = $request->validated();

        $pattern->update([
            'model_name'             => $data['modelName'],
            'reset_instructions'     => $data['resetInstructions'],
            'unlock_sequence'        => $data['unlockSequence'],
        ]);

        return new PadlockPatternResource($pattern->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PadlockPattern $padlockPattern)
    {
        //
    }
}
