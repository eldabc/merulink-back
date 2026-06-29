<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Schedule;
use App\Models\SchedulePlanning;
use App\Models\Vacation;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class ScheduleAutofillService
{
    protected HolidayService $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    /**
     * Procesa y genera de manera masiva la planificación y turnos correspondientes.
     *
     * @param int $departmentId
     * @param Carbon $start
     * @param Carbon $end
     * @param array $activeShift Estructura en array del turno maestro seleccionado
     * @param int|null $planningId Si existe, se limpian sus turnos anteriores
     * @return int El ID de la planificación generada o actualizada
     */
    public function execute(int $departmentId, Carbon $start, Carbon $end, array $activeShift, ?int $planningId = null): int
    {
        // Obtener la lista de feriados mapeados en rango
        $holidays = $this->holidayService->getHolidaysInRange($start, $end);

        // Traer los empleados activos
        $employees = Employee::where('department_id', $departmentId)->where('status', true)->get();

        if ($employees->isEmpty()) {
            throw new \Exception('No hay empleados activos en este departamento.');
        }

        // Traer las vacaciones que cruzan la quincena
        $vacations = Vacation::whereIn('employee_id', $employees->pluck('id'))
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                    ->orWhereBetween('end', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start', '<=', $start->format('Y-m-d'))
                            ->where('end', '>=', $end->format('Y-m-d'));
                    });
            })->get();

        // Operaciones de escritura en la transacción
        return DB::transaction(function () use ($departmentId, $start, $end, $activeShift, $holidays, $employees, $vacations, $planningId) {
            
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

            $period = CarbonPeriod::create($start, $end);
            $newSchedulesData = [];

            foreach ($period as $currentDay) {
                $dateString = $currentDay->format('Y-m-d');

                // Excluir fines de semana
                if ($currentDay->isWeekend()) continue;

                // Excluir Feriados (Fijos y Google)
                if (array_key_exists($dateString, $holidays)) continue;

                // Iterar empleados y validar vacaciones
                foreach ($employees as $employee) {
                    $onVacation = $vacations->where('employee_id', $employee->id)
                        ->contains(function ($vacation) use ($dateString) {
                            return $dateString >= $vacation->start && $dateString <= $vacation->end;
                        });

                    if ($onVacation) continue;

                    // Mapeo masivo en memoria
                    $newSchedulesData[] = [
                        'date'                    => $dateString,
                        'employee_id'             => $employee->id,
                        'shift_id'                => $activeShift['id'],
                        'letter_shift'            => $activeShift['letterShift'] ?? $activeShift['letter_shift'] ?? null,
                        'color'                   => $activeShift['color'],
                        'code'                    => $activeShift['code'],
                        'night_shift'             => $activeShift['nightShift'] ?? $activeShift['night_shift'] ?? 0,
                        'type_shift'              => $activeShift['typeShift'] ?? $activeShift['type_shift'] ?? null,
                        'check_in_time'           => $activeShift['checkInTime'] ?? $activeShift['check_in_time'] ?? null,
                        'check_out_time'          => $activeShift['checkOutTime'] ?? $activeShift['check_out_time'] ?? null,
                        'rest_period_time'        => $activeShift['restPeriodTime'] ?? $activeShift['rest_period_time'] ?? null,
                        'rest_period_unit_time'   => $activeShift['restPeriodUnitTime'] ?? $activeShift['rest_period_unit_time'] ?? null,
                        'active_period_time'      => $activeShift['activePeriodTime'] ?? $activeShift['active_period_time'] ?? null,
                        'active_period_unit_time' => $activeShift['activePeriodUnitTime'] ?? $activeShift['active_period_unit_time'] ?? null,
                        'total_period_time'       => $activeShift['totalPeriodTime'] ?? $activeShift['total_period_time'] ?? null,
                        'total_period_unit_time'  => $activeShift['totalPeriodUnitTime'] ?? $activeShift['total_period_unit_time'] ?? null,
                        'allow_exit'              => $activeShift['allowExit'] ?? $activeShift['allow_exit'] ?? 1,
                        'allow_re_scanned'        => $activeShift['allowReScanned'] ?? $activeShift['allow_re_scanned'] ?? 1,
                        'schedule_planning_id'    => $planningId,
                        'created_at'              => now(),
                        'updated_at'              => now(),
                    ];
                }
            }

            // Inserción masiva única por lote
            if (!empty($newSchedulesData)) {
                Schedule::insert($newSchedulesData);
            }

            return $planningId;
        });
    }
}