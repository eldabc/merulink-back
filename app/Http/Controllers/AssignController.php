<?php

namespace App\Http\Controllers;

use App\Models\Assign;
use App\Models\Padlock;
use App\Models\Locker;
use Illuminate\Http\Request;
use App\Http\Resources\AssignResource;
use App\Http\Requests\StoreAssingRequest;
use Illuminate\Support\Facades\DB;

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
        return DB::transaction(function () use ($data) {
            $assign = Assign::create([
                'assign_code' => $data['assignCode'], // Si es nullable, puedes generar uno
                'assign_date' => $data['assignDate'],
                'locker_id'   => $data['locker']['id'],
                'padlock_id'  => $data['locker']['padlock']['id'],
                'employee_id' => $data['employee']['id'] ?? null,
            ]);
            

            Locker::where('id', $data['locker']['id'])->update([
                'status' => 'Ocupado'
            ]);

            Padlock::where('id', $data['locker']['padlock']['id'])->update([
                'status' => 'Asignado'
            ]);

            return new AssignResource($assign);
        });
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
