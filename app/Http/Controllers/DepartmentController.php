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

use App\Models\Department;
use App\Support\DepartmentCache;
use Illuminate\Support\Facades\Cache;

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
        // $departments = Cache::rememberForever(DepartmentCache::KEY, function () {
            $departments = Department::with('subDepartments', 'positions')->orderByRaw('code::int')->get();
        // });

        return DepartmentResource::collection($departments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        $allowedDepartments = $user?->employee?->roleSnapshot?->departments ?? [];

        // Crear
        $department = Department::create([
            'code' => $data['code'],
            'name' => $data['departmentName'],
        ]);

        // Dar acceso al usuario logueado al departamento recién creado.
        $allowedDepartments[] = $department->id;

        $user?->employee?->roleSnapshot?->update([
            'departments' => $allowedDepartments,
        ]);

        return new DepartmentResource($department->load(['subDepartments', 'positions']));
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

        return new DepartmentResource($department->load(['subDepartments', 'positions']));
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
