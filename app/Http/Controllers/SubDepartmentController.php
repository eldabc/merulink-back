<?php

namespace App\Http\Controllers;

use App\Models\SubDepartment;
use Illuminate\Http\Request;
use App\Http\Resources\SubDepartmentResource;
use App\Http\Requests\StoreSubDepartmentRequest;

class SubDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sub_departments = SubDepartment::with('department','positions')->get();
        return SubDepartmentResource::collection($sub_departments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubDepartmentRequest $request)
    {
        $data = $request->validated();
        $sub_department = SubDepartment::create([
            'code'          => $data['code'],
            'name'          => $data['name'],
            'department_id' => $data['department']['id'], 
        ]);

        return new SubDepartmentResource($sub_department->load('department','positions'));   
    }

    /**
     * Display the specified resource.
     */
    public function show(SubDepartment $subDepartment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreSubDepartmentRequest $request, SubDepartment $subdepartment)
    {
        $data = $request->validated();

        $subdepartment->update([
            'code'          => $data['code'],
            'name'          => $data['name'],
            'department_id' => $data['department']['id']
        ]);

        return new SubDepartmentResource($subdepartment->load('department','positions'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubDepartment $subdepartment)
    {
        $subdepartment->delete();

        return response()->json([
            'message' => "El Subdepartamento {$subdepartment->name} ha sido eliminado correctamente."
        ], 200);
    }
}
