<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\StoreShiftRequest;
use App\Http\Resources\ShiftResource;
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shifts = Shift::with('department')->get();
        return ShiftResource::collection($shifts);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShiftRequest $request)
    {
        $data = $request->validated();
        $shift = Shift::create($data);

        return new ShiftResource($shift->load('department'));

    }

    /**
     * Display the specified resource.
     */
    public function show(Shift $shift)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreShiftRequest $request, Shift $shift)
    {
        $data = $request->validated();
        $shift->update($data);
        return new ShiftResource($shift->load('department'));

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shift $shift)
    {
        if ($shift->schedules()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar: este turno tiene horarios asociados.'
            ], 422);
        }

        $shift->delete();

        return response()->json([
            'message' => "El turno {$shift->description} ha sido eliminado correctamente."
        ], 200);
    }

    /**
     * Obtiene los códigos existentes y genera el próximo código sugerido
     * para nuevo turno para departamento específico.
     */
    public function getNextCodeData($department_id)
    {
        // Validar
        $validator = Validator::make(['department_id' => $department_id], [
            'department_id' => 'required|integer|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'El departamento seleccionado no es válido o no existe.'
            ], 422);
        }

        // 'value' extrae directamente el string de la columna 'name'
        $departmentName = Department::where('id', $department_id)->value('name');

        // Limpiar espacios
        $cleanedName = trim($departmentName);

        // Caso especial: "Alimentos y Bebidas" (insensible mayúsculas/minúsculas)
        if (strcasecmp($cleanedName, 'Alimentos y Bebidas') === 0) {
            $prefix = 'AB';
        } else {
            $prefix = Str::upper(Str::substr($cleanedName, 0, 2));
        }

        // Consulta para turnos del departamento
        $query = Shift::where('department_id', $department_id);

        $currentCount = $query->count();
        $existingCodes = $query->pluck('code');
        $nextNumber = $currentCount + 1;

        // str_pad añade ceros a la izquierda (ej: AB-01, AL-02)
        $suggestedCode = $prefix . '-' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

        return response()->json([
            'data' => [
                'existingCodes'  => $existingCodes,
                'suggestedCode'  => $suggestedCode
            ]
        ], 200);
    }
}
