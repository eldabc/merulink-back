<?php

namespace App\Http\Controllers;

use App\Models\Assign;
use App\Models\Padlock;
use App\Models\Locker;
use Illuminate\Http\Request;
use App\Http\Resources\AssignResource;
use App\Http\Requests\StoreAssingRequest;
use Illuminate\Support\Facades\DB;
use App\Enums\LockerStatus;
use App\Enums\PadlockStatus;

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

            $employeeId = data_get($data, 'employee.id');

            $assign = Assign::updateOrCreate(
                ['locker_id' => $data['locker']['id']],

                [
                 'assign_code' => $data['assignCode'],
                 'assign_date' => $data['assignDate'],
                 'padlock_id'  => $data['locker']['padlock']['id'],
                 'employee_id' => $employeeId,
                ]
            );

            $locker_status = $employeeId ? LockerStatus::OCCUPIED : LockerStatus::MATCHED;
            

            Locker::where('id', $data['locker']['id'])->update([
                'status' => $locker_status
            ]);

            Padlock::where('id', $data['locker']['padlock']['id'])->update([
                'status' => PadlockStatus::ASSIGNED
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
