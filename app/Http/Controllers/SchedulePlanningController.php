<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\SchedulePlanning;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\SchedulePlanningResource;
use App\Http\Requests\SchedulePlanningRequest;


class SchedulePlanningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SchedulePlanning::with(['department', 'schedules']); 
        
        // Filtro
        if ($request->filled('start') && $request->filled('end') && $request->filled('departmentId')) {
            $query->where('department_id', $request->departmentId);
            $query->where('start', $request->start);
            $query->where('end', $request->end);
        }

        // $query->orderBy('date', 'asc');
        $schedules = $query->get();

        return SchedulePlanningResource::collection($schedules);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SchedulePlanningRequest $request)
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();
            // Crear la cabecera en schedule_plannings
            $planning = SchedulePlanning::create([
                'start' => $data['start'],
                'end' => $data['end'],
                'status' => $data['status'],
                'department_id' => $data['department_id'],
                'observations' => $data['observations'],
            ]);

            // Recorrer los empleados y sus fechas asignadas
            foreach ($data['schedules'] as $employeeSchedule) {
                $employeeId = $employeeSchedule['employeeId'];

                foreach ($employeeSchedule['dates'] as $date => $dateData) {
                    $shift = $dateData['shift'] ?? null;

                    if (!$shift) {
                        continue;
                    }

                    // Si es Vacación (ID -1) shift_id va como null para saltar el constraint
                    $shiftId = (int) $shift['id'] === -1 ? null :  $shift['id'];

                    // Ignorar días Libres
                    if ($shiftId === 0) {
                        continue;
                    }

                    // Generar código único
                    $uniqueCode = 'SCH-' . $employeeId . '-' . $date;

                    Schedule::create([
                        'date'                 => $date,
                        'employee_id'          => $employeeId,
                        'schedule_planning_id' => $planning->id,
                        'code'                 => $uniqueCode,
                        'shift_id'             => $shiftId,
                        'letter_shift'         => $shift['letterShift'],
                        'color'                => $shift['color'],
                        'night_shift'          => $shift['nightShift'],
                        'type_shift'           => $shift['typeShift'],
                        'check_in_time'            => $shift['checkInTime'],
                        'check_out_time'           => $shift['checkOutTime'],
                        'rest_period_time'         => $shift['restPeriodTime'],
                        'rest_period_unit_time'    => $shift['restPeriodUnitTime'],
                        'active_period_time'       => $shift['activePeriodTime'],
                        'active_period_unit_time'  => $shift['activePeriodUnitTime'],
                        'total_period_time'        => $shift['totalPeriodTime'],
                        'total_period_unit_time'   => $shift['totalPeriodUnitTime'],
                        'allow_exit'               => $shift['allowExit'],
                        'allow_re_scanned'         => $shift['allowReScanned'],
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Planificación e historial de turnos guardados exitosamente.'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al guardar la planificación: ' . $e->getMessage()
            ], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(SchedulePlanning $schedulePlanning)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SchedulePlanning $schedulePlanning)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SchedulePlanning $schedulePlanning)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SchedulePlanning $schedulePlanning)
    {
        //
    }
}
