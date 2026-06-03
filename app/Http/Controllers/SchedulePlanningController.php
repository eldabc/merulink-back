<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Employee;
use App\Models\SchedulePlanning;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ShiftResource;
use App\Http\Resources\SchedulePlanningResource;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\EmployeeFilterScheduleResource;
use App\Http\Requests\SchedulePlanningRequest;
use App\Enums\SystemShift;

use App\Services\ShiftVisualIdentityService;

class SchedulePlanningController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SchedulePlanning::with(['department', 'schedules', 'schedules.employee']); 
        
        // Filtro
        if ($request->filled('start') && $request->filled('end')) {
            $query->where('start', $request->start);
            $query->where('end', $request->end);

        }

        if ($request->filled('departmentId')) {
            $query->where('department_id', $request->departmentId);
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
            
            // Crear la cabecera
            $planning = SchedulePlanning::create([
                'start' => $data['start'],
                'end' => $data['end'],
                'month_number' => $data['month_number'],
                'status' => $data['status'],
                'department_id' => $data['department_id'],
                'observations' => $data['observations'],
            ]);

            // Recorrer los empleados y sus fechas asignadas para registrar
            $this->saveSchedulesBatch($data['schedules'], $planning);

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
     * Update the specified resource in storage.
     */
    public function update(SchedulePlanningRequest $request, SchedulePlanning $schedulePlanning)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            
            // Actualizar cabecera
            $schedulePlanning->update([
                'start' => $data['start'],
                'end' => $data['end'],
                'month_number' => $data['month_number'],
                'status' => $data['status'],
                'department_id' => $data['department_id'],
                'observations' => $data['observations'],
            ]);

            $schedulePlanning->schedules()->delete();

            // Recorrer los empleados y sus fechas asignadas para registrar
            $this->saveSchedulesBatch($data['schedules'], $schedulePlanning);

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
     * Remove the specified resource from storage.
     */
    public function destroy(SchedulePlanning $schedulePlanning)
    {
        //
    }

    /**
     * Trae los empleados para schedule
     */
    public function filterSchedule(Request $request, ShiftVisualIdentityService $scheduleShiftService)
    {
        if (!$request->filled(['departmentId', 'start', 'end'])) {
            return response()->json([
                'message' => 'Los parámetros departmentId, start y end son obligatorios.'
            ], 400); 
        }

        $start = $request->input('start');
        $end = $request->input('end');

        $planning = SchedulePlanning::query()
            ->where('department_id', $request->departmentId)
            ->whereDate('start', $start)
            ->whereDate('end', $end)
            ->first();

        $today = Carbon::now()->startOfDay();
        $periodEnd = Carbon::parse($end)->startOfDay();
        $isClosedInDB = $planning && $planning->status === 'closed';

        // Valida si fecha actual es mayor que el fin de quincena
        $isExpiredByDate = $today->greaterThan($periodEnd);

        // El periodo se trata como cerrado si se cumple cualquiera de las dos
        $isClosed = $isClosedInDB || $isExpiredByDate;

        $query = Employee::query();

        // Filtro por departamento a través de la posición
        $query->whereHas('position', function ($q) use ($request) {
            $q->where('department_id', $request->departmentId);
        });

        // ESTRATEGIA DE EMPLEADOS HISTÓRICOS VS ACTIVOS O RETIROS A MITAD DE QUINCENA
        // Agrupar las condiciones dinámicas de estatus / históricos
        $query->where(function ($mainGroup) use ($isClosed, $start, $end) {

            if ($isClosed) {
                // Si ya está cerrado o expiró se listan todos los empleados que tengan turnos en esta quincena específica
                $mainGroup->whereHas('schedules', function ($q) use ($start, $end) {
                    $q->whereBetween('date', [$start, $end]);
                });
            } else {
                // ABIERTO/NUEVO: Empleado califica si está activo en el sistema,
                // O si está inactivo PERO su fecha de retiro ocurrió durante la quincena
                $mainGroup->where(function ($q) use ($start) {
                    $q->where('status', true)
                    ->orWhere(function ($sub) use ($start) {
                        $sub->where('status', false)
                            ->whereNotNull('retire_date')
                            ->where('retire_date', '>=', $start);
                    });
                });

                
            }
            $mainGroup->orWhereHas('vacations', function ($v) use ($start, $end) {
                $v->where(function ($vQuery) use ($start, $end) {
                    $vQuery->whereBetween('start', [$start, $end])
                        ->orWhereBetween('end', [$start, $end])
                        ->orWhere(function ($deep) use ($start, $end) {
                            $deep->where('start', '<=', $start)
                                    ->where('end', '>=', $end);
                        });
                });
            });

        });

        // Eager Loading seguro
        $employees = $query->with([
            'position.department', 
            'position.subDepartment',
            'schedules' => function ($q) use ($start, $end) {
                $q->whereBetween('date', [$start, $end]);
            },
            'vacations' => function ($q) use ($start, $end) {
                $q->where(function ($vQuery) use ($start, $end) {
                    $vQuery->whereBetween('start', [$start, $end])
                        ->orWhereBetween('end', [$start, $end])
                        ->orWhere(function ($deep) use ($start, $end) {
                            $deep->where('start', '<=', $start)
                                 ->where('end', '>=', $end);
                        });
                });
            }
        ])->get();

        if ($isClosed) {
            $shifts = Schedule::query()
                ->where('schedule_planning_id', $planning->id)
                ->select([
                    'shift_id as id',
                    'code',
                    'letter_shift as letterShift',
                    'color',
                    'night_shift as nightShift',
                    'type_shift as typeShift',
                    'check_in_time as checkInTime',
                    'check_out_time as checkOutTime',
                    'active_period_time as activePeriodTime',
                    'active_period_unit_time as activePeriodUnitTime',
                    'rest_period_time as restPeriodTime',
                    'rest_period_unit_time as restPeriodUnitTime',
                    'total_period_time as totalPeriodTime',
                    'total_period_unit_time as totalPeriodUnitTime',
                    'allow_exit as allowExit',
                    'allow_re_scanned as allowReScanned',
                ])
                ->distinct()
                ->get()
                ->unique('shift_id')
                ->values();
        } else {
            $departmentShifts = Shift::where('department_id', $request->departmentId)
                ->where('available', 'yes')
                ->orderBy('check_in_time')
                ->with('department')
                ->get();

            $shiftsCollection = ShiftResource::collection(
                $scheduleShiftService->apply($departmentShifts)
            );

            // Inyectar shifts del sistema
            $shifts = collect($shiftsCollection)
                ->prepend(SystemShift::FREE->getData())
                ->prepend(SystemShift::RETIREMENT->getData())
                ->prepend(SystemShift::VACATIONS->getData());

        }

        // Agrupación por Subdepartamento y Retorno
        $groupedEmployees = $employees->groupBy(function ($employee) {
            return $employee->position->subDepartment->name ?? 'Sin Subdepartamento';
        });

        return response()->json([
            'id'           => $planning?->id,
            'status'       => $planning?->status,
            'isClosed'     => $isClosed,
            'departmentId' => $planning?->department_id,
            'start'        => $planning?->start ?? $start, // Fallback por si está creando nuevo
            'end'          => $planning?->end ?? $end,     // Fallback por si está creando nuevo
            'monthNumber'  => $planning?->month_number,
            'shifts'       => $shifts,
            'employees'    => $groupedEmployees->map(function ($group) {
                return EmployeeFilterScheduleResource::collection($group);
            }),
        ]);
    }

    private function saveSchedulesBatch(array $schedulesData, SchedulePlanning $planning)
    {
        foreach ($schedulesData as $employeeSchedule) {
            $employeeId = $employeeSchedule['employeeId'];

            foreach ($employeeSchedule['dates'] as $date => $dateData) {
                $shift = $dateData['shift'] ?? null;

                if (!$shift) {
                    continue;
                }

                $shiftId = (int) $shift['id'];

                // Ignorar System Shifts
                if (isset($shift['isSystemShift']) && $shift['isSystemShift'] === true) {
                    continue;
                }

                Schedule::create([
                    'date'                    => $date,
                    'employee_id'             => $employeeId,
                    'schedule_planning_id'    => $planning->id,
                    'code'                    => $shift['code'],
                    'shift_id'                => $shiftId,
                    'letter_shift'            => $shift['letterShift'],
                    'color'                   => $shift['color'],
                    'night_shift'             => $shift['nightShift'],
                    'type_shift'              => $shift['typeShift'],
                    'check_in_time'           => $shift['checkInTime'],
                    'check_out_time'          => $shift['checkOutTime'],
                    'rest_period_time'        => $shift['restPeriodTime'] ?? null,
                    'rest_period_unit_time'   => $shift['restPeriodUnitTime'] ?? null,
                    'active_period_time'      => $shift['activePeriodTime'] ?? null,
                    'active_period_unit_time' => $shift['activePeriodUnitTime'] ?? null,
                    'total_period_time'       => $shift['totalPeriodTime'] ?? null,
                    'total_period_unit_time'  => $shift['totalPeriodUnitTime'] ?? null,
                    'allow_exit'              => $shift['allowExit'] ?? null,
                    'allow_re_scanned'        => $shift['allowReScanned'] ?? null,
                ]);
            }
        }
    }
}
