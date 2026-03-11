<?php

namespace App\Http\Controllers;

use App\Models\Padlock;
use Illuminate\Http\Request;
use App\Http\Requests\StorePadlockRequest;
use App\Http\Requests\UpdatePadlockRequest;
use App\Http\Resources\PadlockResource;

class PadlockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Padlock::query();

        // Filtro candados SIN asignación activa
        if ($request->boolean('available')) {
            $query->whereDoesntHave('assignment'); 
        }

        $padlocks = $query->get();
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
    public function update(StorePadlockRequest $request, Padlock $padlock)
    {
        $data = $request->validated();

        $padlock->update([
            'serial'   => $data['serial'],
            'pass'     => $data['pass'],
            'status'   => $data['status'],
        ]);

        return new PadlockResource($padlock);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Padlock $padlock)
    {
        if ($padlock->status->isAssigned()) {
            return response()->json([
                'message' => 'No se puede eliminar un candado que está asignado.'
            ], 422);
        }

        $padlock->delete();

        return response()->json([
            'message' => "El candado {$padlock->serial} ha sido eliminado correctamente."
        ], 200);
    }
}
