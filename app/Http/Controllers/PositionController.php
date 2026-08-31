<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\SubDepartment;
use Illuminate\Http\Request;
use App\Http\Resources\PositionResource;
use App\Http\Requests\StorePositionRequest;
use Illuminate\Support\Facades\DB;


class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $positions = Position::orderByRaw('code::int')->get();
        return PositionResource::collection($positions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePositionRequest $request)
    {
        $data = $request->validated();
        return DB::transaction(function () use ($data) {

            if ($data['sub_department_name'] && $data['sub_department_code']) {
                $subDepartment = SubDepartment::firstOrCreate(
                    [
                        'name' => $data['sub_department_name'], 
                        'department_id' => $data['department_id']
                    ],
                    ['code' => $data['sub_department_code']]
                );
                $data['sub_department_id'] = $subDepartment->id;
            }

            // Crear
            $position = Position::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'department_id' => $data['department_id'],
                'sub_department_id' => $data['sub_department_id'],
            ]);

            return new PositionResource($position);
        });

    }

    /**
     * Display the specified resource.
     */
    public function show(Position $position)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePositionRequest $request, Position $position)
    {
        $data = $request->validated();
        return DB::transaction(function () use ($data, $position) {

            if ($data['sub_department_name'] && $data['sub_department_code']) {
                $subDepartment = SubDepartment::firstOrCreate(
                    [
                        'name' => $data['sub_department_name'], 
                        'department_id' => $data['department_id']
                    ],
                    ['code' => $data['sub_department_code']]
                );
                $data['sub_department_id'] = $subDepartment->id;
            }

            // Crear
            $position->update([
                'code' => $data['code'],
                'name' => $data['name'],
                'department_id' => $data['department_id'],
                'sub_department_id' => $data['sub_department_id'],
            ]);

            return new PositionResource($position);
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        if ($position->employees()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar: este cargo tiene empleados asociados.'
            ], 422);
        }

        $position->delete();

        return response()->json([
            'message' => "El cargo {$position->name} ha sido eliminado correctamente."
        ], 200);
    }
}
