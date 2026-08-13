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
        $employees = Employee::where('department_id', $departmentId)
                        ->whereHas('employeePeriods', fn($q) => $q->activeInPeriod($start, $end))->with([
                            'employeePeriods' => fn($q) => $q->activeInPeriod($start, $end),
                            'vacations'       => fn($q) => $q->overlapPeriod($start, $end),
                        ])->get();

        if ($employees->isEmpty()) {
            throw new \Exception('No hay empleados activos en este departamento.');
        }

        // Traer las vacaciones que cruzan la quincena
        $vacations = Vacation::whereIn('employee_id', $employees->pluck('id'))
            ->overlapPeriod($start->format('Y-m-d'), $end->format('Y-m-d'))
            ->get();

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
                    
                    $hasActiveContractThisDay = $employee->employeePeriods->contains(function ($period) use ($dateString) {
                        // Fecha efectiva de baja: retire_date  o scheduled_deactivate_date
                        $deactivationDate = $period->retire_date ?? $period->scheduled_deactivate_date;

                        return $dateString >= $period->hire_date
                            && (is_null($deactivationDate) || $dateString <= $deactivationDate);
                    });

                    // Excluir si ya estaba de baja este día
                    if (!$hasActiveContractThisDay) continue;

                    $onVacation = $vacations->where('employee_id', $employee->id)
                        ->contains(function ($vacation) use ($dateString) {
                            return $dateString >= $vacation->start && $dateString <= $vacation->end;
                        });

                    // Excluir si es día de vacaciones
                    if ($onVacation) continue;

                    // Mapeo masivo en memoria
                    $newSchedulesData[] = [
                        'date'                    => $dateString,
                        'employee_id'             => $employee->id,
                        'shift_id'                => $activeShift['id'],
                        'letter_shift'            => $activeShift['letterShift'] ?? $activeShift['letter_shift'] ?? 'A',
                        'color'                   => $activeShift['color'] ?? '#FBBD08',
                        'code'                    => $activeShift['code'],
                        'night_shift'             => $activeShift['nightShift'] ?? $activeShift['night_shift'],
                        'type_shift'              => $activeShift['typeShift'] ?? $activeShift['type_shift'],
                        'check_in_time'           => $activeShift['checkInTime'] ?? $activeShift['check_in_time'],
                        'check_out_time'          => $activeShift['checkOutTime'] ?? $activeShift['check_out_time'],
                        'rest_period_time'        => $activeShift['restPeriodTime'] ?? $activeShift['rest_period_time'],
                        'rest_period_unit_time'   => $activeShift['restPeriodUnitTime'] ?? $activeShift['rest_period_unit_time'],
                        'active_period_time'      => $activeShift['activePeriodTime'] ?? $activeShift['active_period_time'],
                        'active_period_unit_time' => $activeShift['activePeriodUnitTime'] ?? $activeShift['active_period_unit_time'],
                        'total_period_time'       => $activeShift['totalPeriodTime'] ?? $activeShift['total_period_time'],
                        'total_period_unit_time'  => $activeShift['totalPeriodUnitTime'] ?? $activeShift['total_period_unit_time'],
                        'allow_exit'              => $activeShift['allowExit'] ?? $activeShift['allow_exit'],
                        'allow_re_scanned'        => $activeShift['allowReScanned'] ?? $activeShift['allow_re_scanned'],
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
            
            SchedulePlanning::find($planningId)?->recordHistory('autofill', 'Horario autocompletado');
            return $planningId;
        });
    }
}