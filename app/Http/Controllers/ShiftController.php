<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Department;
use App\Models\Schedule;
use App\Models\SchedulePlanning;
use Illuminate\Support\Str;
use App\Services\ShiftVisualIdentityService;
use Illuminate\Http\Request;
use App\Http\Requests\StoreShiftRequest;
use App\Http\Resources\ShiftResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ShiftVisualIdentityService $scheduleShiftService)
    {
        $query = Shift::query();

        if ($request->filled('departmentId')) {

            $shifts = $query->where('department_id', $request->departmentId)
                ->where('available', 'yes')
                ->orderBy('check_in_time')
                ->with('department')
                ->get();

            $shifts = $scheduleShiftService->apply($shifts);
            
        } else {
            $shifts = $query->with('department')->get();
        }
        
        return ShiftResource::collection($shifts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShiftRequest $request)
    {
        $data = $request->validated();
        $getRegistredShifts = Shift::where('department_id', $data['department_id'])->where('available', 'yes')->pluck('check_in_time');
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
        DB::beginTransaction();

        try {
            // Actualizar
            $shift->update($data);

            if (!empty($shift->available_from)) {
                $availableFrom = $shift->available_from;

                // Buscar IDs de planificaciones abiertas de ESTE departamento que terminen después del available_from
                $planningIds = SchedulePlanning::where('department_id', $shift->department_id)
                    ->where('status', '!=', 'closed')
                    ->where('end', '>=', $availableFrom)
                    ->pluck('id');

                if ($planningIds->isNotEmpty()) {
                    // Actualizar en cascada solo las filas de schedules afectadas
                    Schedule::whereIn('schedule_planning_id', $planningIds)
                        ->where('shift_id', $shift->id)
                        // ->where('date', '>=', $availableFrom) // Solo a partir del día fijado
                        ->update([
                            'code'                   => $shift->code,
                            'night_shift'            => $shift->night_shift,
                            'type_shift'             => $shift->type_shift,
                            'check_in_time'          => $shift->check_in_time,
                            'check_out_time'         => $shift->check_out_time,
                            'rest_period_time'       => $shift->rest_period_time,
                            'rest_period_unit_time'  => $shift->rest_period_unit_time,
                            'active_period_time'     => $shift->active_period_time,
                            'active_period_unit_time'=> $shift->active_period_unit_time,
                            'total_period_time'      => $shift->total_period_time,
                            'total_period_unit_time' => $shift->total_period_unit_time,
                            'allow_exit'             => $shift->allow_exit,
                            'allow_re_scanned'       => $shift->allow_re_scanned,
                        ]);
                }
            }

            DB::commit();

            return new ShiftResource($shift->load('department'));

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
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
