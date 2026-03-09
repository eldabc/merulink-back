<?php

namespace App\Http\Controllers;

use App\Models\Assign;
use Illuminate\Http\Request;
use App\Http\Resources\AssignResource;
use App\Http\Requests\StoreAssingRequest;

class AssignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assigns = Assign::all();
        return AssignResource::collection($assigns);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssingRequest $request)
    {
        $data = $request->validated();
        $assign = Assign::create([
            'assign_code' => $data['assignCode'], // Si es nullable, puedes generar uno
            'assign_date' => $data['assignDate'],
            'locker_id'   => $data['locker']['id'],
            'padlock_id'  => $data['locker']['padlock']['id'],
            'employee_id' => $data['employee']['id'] ?? null,
        ]);
        // TODO: ACTUALIZAR status de locker, padlock

    return new AssignResource($assign);
    }

    /**
     * Display the specified resource.
     */
    public function show(Assign $assign)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Assign $assign)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Assign $assign)
    {
        //
    }
}
