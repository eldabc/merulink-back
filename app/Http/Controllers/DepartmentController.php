<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Resources\DepartmentResource;
use App\Http\Requests\StoreDepartmentRequest;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $departments = Department::with('subDepartments')->get();
        return DepartmentResource::collection($departments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        $data = $request->validated();

        // Crear
        $department = Department::create([
            'code' => $data['code'],
            'name' => $data['departmentName'],
        ]);

        return new DepartmentResource($department);
    }

    /**
     * Display the specified resource.
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreDepartmentRequest $request, Department $department)
    {
        $data = $request->validated();

        $department->update([
            'code' => $data['code'],
            'name' => $data['departmentName'],
        ]);

        return new DepartmentResource($department->load('subDepartments'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        if ($department->subdepartments()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar: este departamento tiene subdepartamentos asociados.'
            ], 422);
        }

        $department->delete();

        return response()->json([
            'message' => "El department {$department->code} ha sido eliminado correctamente."
        ], 200);
    }
}
