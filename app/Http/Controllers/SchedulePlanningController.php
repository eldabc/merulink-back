<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Employee;
use App\Models\Vacation;
use App\Models\SchedulePlanning;
use App\Models\Department;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ShiftResource;
use App\Http\Resources\SchedulePlanningResource;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\EmployeeFilterScheduleResource;
use App\Http\Requests\SchedulePlanningRequest;
use App\Http\Requests\FortnightParamsRequest;
use App\Http\Requests\ToggleAutofillParamsRequest;
use App\Enums\SystemShift;

use App\Services\ShiftVisualIdentityService;
use App\Services\EventToScheduleService;
use App\Services\GoogleCalendarService;
use App\Services\HolidayService;

class SchedulePlanningController extends Controller
{
    
    protected ShiftVisualIdentityService $scheduleShiftService;
    protected EventToScheduleService $eventToScheduleService;
    protected GoogleCalendarService $googleCalendarService;
    protected HolidayService $holidayService;
    
    public function __construct(
        ShiftVisualIdentityService $scheduleShiftService,
        EventToScheduleService $eventToScheduleService,
        GoogleCalendarService $googleCalendarService,
        HolidayService $holidayService,
    ) {
        $this->scheduleShiftService = $scheduleShiftService;
        $this->eventToScheduleService = $eventToScheduleService;
        $this->googleCalendarService = $googleCalendarService;
        $this->holidayService = $holidayService;
    }

    /**
     * Display a listing of the resource.
    */
    public function index(Request $request)
    {
        $query = SchedulePlanning::with(['department', 'schedules', 'schedules.employee']); 
        
        if ($request->filled('start') && $request->filled('end')) {
            $query->where('start', $request->start)
                  ->where('end', $request->end);
        }

        if ($request->filled('departmentId')) {
            $query->where('department_id', $request->departmentId);
        }

        // FILTRADO DE TIEMPO
        if ($request->filled('monthId')) {

            $currentYear = $request->filled('year') ? $request->input('year') : Carbon::now()->year;            
            $query->where('month_number', $request->input('monthId'))
                  ->whereYear('start', $currentYear);
        } else {

            $dateEnd = Carbon::now()->endOfMonth()->format('Y-m-d'); 
            $dateStart = Carbon::now()->subMonths(2)->startOfMonth()->format('Y-m-d');

            // Filtro por el rango de fechas calculado
            $query->whereBetween('start', [$dateStart, $dateEnd]);
        }
        
        $schedules = $query->orderBy('start', 'asc')->get();

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
    public function show(SchedulePlanning $schedulePlanning, Request $request, ShiftVisualIdentityService $scheduleShiftService)
    {
        // 1. Extraemos las variables clave de la planificación cargada
        $start = $schedulePlanning->start;
        $end = $schedulePlanning->end;
        $departmentId = $schedulePlanning->department_id;

        // Forzamos los parámetros en el Request en tiempo de ejecución.
        // Esto hace que el Resource lea 'start' y 'end' sin modificarle una sola línea de código.
        $request->merge([
            'start' => $start,
            'end'   => $end
        ]);

        // 2. Evaluamos el estado reactivo de cierre (Igual que en tu filtro)
        $today = Carbon::now()->startOfDay();
        $periodEnd = Carbon::parse($end)->startOfDay();
        
        $isClosedInDB = $schedulePlanning->status === 'closed';
        $isExpiredByDate = $today->greaterThan($periodEnd);

        // El periodo se trata como cerrado si se cumple cualquiera de las dos
        $isClosed = $isClosedInDB || $isExpiredByDate;

        // 3. Replicamos exactamente la misma consulta de Empleados
        $query = Employee::query();

        // Filtro por departamento a través de la posición
        $query->whereHas('position', function ($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        });

        // Misma estrategia de empleados históricos vs activos o retiros
        $query->where(function ($mainGroup) use ($isClosed, $start, $end) {
            if ($isClosed) {
                $mainGroup->whereHas('schedules', function ($q) use ($start, $end) {
                    $q->whereBetween('date', [$start, $end]);
                });
            } else {
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

        // Eager Loading con el contexto quincenal de la planificación
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

        // 4. Mapeo de la barra lateral de Turnos (Shifts) según estado
        if ($isClosed) {
            $shifts = Schedule::query()
                ->where('schedule_planning_id', $schedulePlanning->id)
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
            $departmentShifts = Shift::where('department_id', $departmentId)
                ->where('available', 'yes')
                ->orderBy('check_in_time')
                ->with('department')
                ->get();

            $shiftsCollection = ShiftResource::collection(
                $this->scheduleShiftService->apply($departmentShifts)
            );

            // Inyectamos los del sistema convirtiéndolos a objetos limpios
            $shifts = collect($shiftsCollection)
                ->prepend((object) SystemShift::FREE->getData())
                ->prepend((object) SystemShift::RETIREMENT->getData())
                ->prepend((object) SystemShift::VACATIONS->getData());
        }

        // 5. Agrupación por Subdepartamento para AG Grid
        $groupedEmployees = $employees->groupBy(function ($employee) {
            return $employee->position->subDepartment->name ?? 'Sin Subdepartamento';
        });

        // 6. Respuesta limpia y estructurada
        return response()->json([
            'id'           => $schedulePlanning->id,
            'status'       => $schedulePlanning->status,
            'observations' => $schedulePlanning->observations,
            'isClosed'     => $isClosed,
            'departmentId' => $departmentId,
            'start'        => $start,
            'end'          => $end,
            'monthNumber'  => $schedulePlanning->month_number,
            'shifts'       => $shifts,
            'employees'    => $groupedEmployees->map(function ($group) {
                return EmployeeFilterScheduleResource::collection($group);
            }),
        ]);
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
        if ($schedulePlanning->status === 'closed') {
            return response()->json([
                'message' => 'No se puede eliminar: este horario tiene estado cerrado.'
            ], 422);
        }

        try {
            DB::transaction(function () use ($schedulePlanning) {            
                $schedulePlanning->schedules()->delete();
                $schedulePlanning->delete();
            });

            return response()->json([
                'message' => "El horario ha sido eliminado correctamente."
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al intentar eliminar el horario.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleAutofill(ToggleAutofillParamsRequest $request)
    {
        $data = $request->validated();
        try {
            
            Department::where('id', $data['departmentId'])->update(['autofill_fortnight' => $data['autofillFortnight']]);
            
            return response()->json([
                'status'            => 'success',
                'message'           => 'Configuración de automatización actualizada correctamente.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al cambiar indicador de automatización: ' . $e->getMessage()
            ], 500);
        }
        
    }

    public function autofill(FortnightParamsRequest $request)
    {
       $data = $request->validated();

       try {
           
            $departmentId = $data['departmentId'];
            $start = Carbon::parse($data['start']);
            $end = Carbon::parse($data['end']);
            $activeShift = $data['shift'];
            $planningId = $data['id'];
            $autofillFortnight = $data['autofillFortnight'];

            // Obtener la lista de feriados en este rango
            $holidays = $this->holidayService->getHolidaysInRange($start, $end);

            // Traer los empleados activos
            $employees = Employee::where('department_id', $departmentId)->where('status', true)->get();

            if ($employees->isEmpty()) {
                return response()->json(['message' => 'No hay empleados activos en este departamento.'], 422);
            }

            // Traer las vacaciones que chocan con este rango de quincena para optimizar queries
            $vacations = Vacation::whereIn('employee_id', $employees->pluck('id'))
                ->where(function ($query) use ($start, $end) {
                    $query->whereBetween('start', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                        ->orWhereBetween('end', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                        ->orWhere(function ($q) use ($start, $end) {
                            $q->where('start', '<=', $start->format('Y-m-d'))
                                ->where('end', '>=', $end->format('Y-m-d'));
                        });
                })->get();

            return DB::transaction(function () use ($departmentId, $start, $end, $activeShift, $holidays, $employees, $vacations, $planningId, $request, $autofillFortnight) {
                 
                Department::where('id', $departmentId)->update(['autofill_fortnight' => $autofillFortnight]);
                if ($planningId) {
                    Schedule::where('schedule_planning_id', $planningId)->delete();
                } else {
                    $newPlanning = SchedulePlanning::create([
                        'start' => $start->format('Y-m-d'),
                        'end' => $end->format('Y-m-d'),
                        'month_number' => $start->format('n'),
                        'department_id' => $departmentId,
                    ]);
                    $planningId = $newPlanning->id;
                }

                // Generar el periodo de días de la quincena
                $period = CarbonPeriod::create($start, $end);
                $newSchedulesData = [];

                foreach ($period as $currentDay) {
                    $dateString = $currentDay->format('Y-m-d');

                    // Excluir Sábados y Domingos
                    if ($currentDay->isWeekend()) continue;

                    // Excluir Feriados (Fijos y Rotativos de Google)
                    if (array_key_exists($dateString, $holidays)) {
                        continue;
                    }

                    // Asignar el turno a cada empleado si cumple las reglas individuales
                    foreach ($employees as $employee) {
                        
                        $onVacation = $vacations->where('employee_id', $employee->id)
                            ->contains(function ($vacation) use ($dateString) {
                                return $dateString >= $vacation->start && $dateString <= $vacation->end;
                            });

                        if ($onVacation) continue; // Excluir si el empleado está de vacaciones este día en específico

                        $newSchedulesData[] = [
                            'date'                    => $dateString,
                            'employee_id'             => $employee->id,
                            'shift_id'                => $activeShift['id'],
                            'letter_shift'            => $activeShift['letterShift'] ,
                            'color'                   => $activeShift['color'],
                            'code'                    => $activeShift['code'],
                            'night_shift'             => $activeShift['nightShift'],
                            'type_shift'              => $activeShift['typeShift'],
                            'check_in_time'           => $activeShift['checkInTime'],
                            'check_out_time'          => $activeShift['checkOutTime'],
                            'rest_period_time'        => $activeShift['restPeriodTime'],
                            'rest_period_unit_time'   => $activeShift['restPeriodUnitTime'],
                            'active_period_time'      => $activeShift['activePeriodTime'],
                            'active_period_unit_time' => $activeShift['activePeriodUnitTime'],
                            'total_period_time'       => $activeShift['totalPeriodTime'],
                            'total_period_unit_time'  => $activeShift['totalPeriodUnitTime'],
                            'allow_exit'              => $activeShift['allowExit'],
                            'allow_re_scanned'        => $activeShift['allowReScanned'],
                            'schedule_planning_id'    => $planningId,
                            'created_at'              => now(),
                            'updated_at'              => now(),
                        ];
                    }
                }


                // Inserción masiva en BD
                if (!empty($newSchedulesData)) {
                    Schedule::insert($newSchedulesData);
                }
            
                return $this->filterSchedule($request);
            });

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Error al guardar la quincena: ' . $e->getMessage()
            ], 500);
        }

    }

    /**
     * Trae los empleados para schedule
     */
    public function filterSchedule(Request $request)
    {
        if (!$request->filled(['departmentId', 'start', 'end'])) {
            return response()->json([
                'message' => 'Los parámetros departmentId, start y end son obligatorios.'
            ], 400); 
        }

        $start = $request->input('start');
        $end = $request->input('end');
        $departmentId = $request->departmentId;

        $planning = SchedulePlanning::query()
            ->where('department_id', $departmentId)
            ->whereDate('start', $start)
            ->whereDate('end', $end)
            ->first();

        $today = Carbon::now()->startOfDay();
        $periodEnd = Carbon::parse($end)->startOfDay();
        $isClosedInDB = $planning && $planning->status === 'closed';

        // Valida si fecha actual es mayor que el fin de quincena
        $isExpiredByDate = $today->greaterThan($periodEnd);

        // Se trata como cerrado si se cumple cualquiera de las dos
        $isClosed = $isClosedInDB || $isExpiredByDate;

        $query = Employee::query();

        // Filtro por departamento a través de la posición
        $query->whereHas('position', function ($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        });

        // EMPLEADOS HISTÓRICOS VS ACTIVOS O RETIROS A MITAD DE QUINCENA
        $query->where(function ($mainGroup) use ($isClosed, $start, $end) {

            if ($isClosed) {
                // Si ya está cerrado o expiró se listan todos los empleados que tengan turnos en esta quincena específica
                $mainGroup->whereHas('schedules', function ($q) use ($start, $end) {
                    $q->whereBetween('date', [$start, $end]);
                });
            } else {
                // Empleado califica si está activo en el sistema o si está inactivo PERO su fecha de retiro ocurrió durante la quincena
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
                ->where('schedule_planning_id', $planning?->id)
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
            // Obtener los turnos maestros que cumplen el available_from
            $departmentShifts = Shift::where('department_id', $departmentId)
                ->where('available', 'yes')
                ->where('available_from', '<=', $end)
                ->orderBy('check_in_time')
                ->with('department')
                ->get();

            $liveShiftIds = $departmentShifts->pluck('id')->toArray();

            // Busca en Schedules turnos asignados que NO estén en la lista live
            $historicalSchedules = Schedule::query()
                ->where('schedule_planning_id', $planning?->id)
                ->whereNotIn('shift_id', $liveShiftIds) // Solo los perdidos por el available_from
                ->distinct()
                ->get()
                ->unique('shift_id');

            // Transformar los registros de Schedule en instancias de Shift
            $historicalShifts = $historicalSchedules->map(function ($schedule) use ($request, $departmentId) {
                $mockShift = new Shift();
                
                // Asignar manualmente los atributos usando snake_case
                $mockShift->forceFill([
                    'id' => $schedule->shift_id,
                    'code' => $schedule->code,
                    'letter_shift' => $schedule->letter_shift,
                    'color' => $schedule->color,
                    'night_shift' => $schedule->night_shift,
                    'type_shift' => $schedule->type_shift,
                    'check_in_time' => $schedule->check_in_time,
                    'check_out_time' => $schedule->check_out_time,
                    'active_period_time' => $schedule->active_period_time,
                    'active_period_unit_time' => $schedule->active_period_unit_time,
                    'rest_period_time' => $schedule->rest_period_time,
                    'rest_period_unit_time' => $schedule->rest_period_unit_time,
                    'total_period_time' => $schedule->total_period_time,
                    'total_period_unit_time' => $schedule->total_period_unit_time,
                    'allow_exit' => $schedule->allow_exit,
                    'allow_re_scanned' => $schedule->allow_re_scanned,
                    'department_id' => $departmentId,
                    'available' => 'yes',
                    // Se coloca now() para evitar errores
                    'available_from' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Forzar que la relación 'schedules' devuelva true sin ir a la BD
                $mockShift->setRelation('schedules', collect([$schedule])); 

                return $mockShift;
            });

            // Unificar ambos mundos en una sola colección Eloquent
            $mergedShifts = $departmentShifts->concat($historicalShifts);

            $shiftsCollection = ShiftResource::collection(
                $this->scheduleShiftService->apply($mergedShifts)
            );

            $shifts = $shiftsCollection;

        }

        // Agrupación por Subdepartamento
        $groupedEmployees = $employees->groupBy(function ($employee) {
            return $employee->position->subDepartment->name ?? 'Sin Subdepartamento';
        });

        // Buscar eventos que crucen con la quincena y tengan coloring_day
        $events = $this->eventToScheduleService->getHighlightedEventsForPeriod($start, $end);
        $carbonStart = Carbon::parse($start);
        $carbonEnd = Carbon::parse($end);

        // OBTENER FERIADOS (Fijos + Google Calendar unificados)
        $holidaysMap = $this->holidayService->getHolidaysInRange($carbonStart, $carbonEnd);


        return response()->json([
            'id'           => $planning?->id,
            'status'       => $planning?->status,
            'observations' => $planning?->observations,
            'isClosed'     => $isClosed,
            'departmentId' => $planning?->department_id ?? $departmentId,
            'autofillFortnight' => $planning?->department?->autofill_fortnight,
            'start'        => $planning?->start ?? $start,
            'end'          => $planning?->end ?? $end,
            'monthNumber'  => $planning?->month_number,
            'shifts'       => collect($shifts)->prepend(SystemShift::FREE->getData()), // Inyectar shift del sistema
            'employees'    => $groupedEmployees->map(function ($group) use ($events, $shifts, $isClosed, $holidaysMap, $planning) {
                return $group->map(function ($employee) use ($events, $shifts, $isClosed, $holidaysMap, $planning) {
                    return new EmployeeFilterScheduleResource($employee, $events, $isClosed, $shifts, $holidaysMap, $planning?->id);
                });
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
