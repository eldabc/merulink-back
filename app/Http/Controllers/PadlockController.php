<?php

namespace App\Http\Controllers;

use App\Models\Padlock;
use App\Http\Requests\StorePadlockRequest;
use App\Http\Requests\UpdatePadlockRequest;
use App\Http\Resources\PadlockResource;

class PadlockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $padlocks = Padlock::all();
        return PadlockResource::collection($padlocks);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePadlockRequest $request)
    {
        $data = $request->validated();

        // Crear
        $locker = Padlock::create([
            'serial'             => $data['serial'],
            'pass'               => $data['pass'],
            'status'             => $data['status'],
             
        ]);

        return new PadlockResource($locker);
    }

    /**
     * Display the specified resource.
     */
    public function show(Padlock $padlock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePadlockRequest $request, Padlock $padlock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Padlock $padlock)
    {
        //
    }
}
