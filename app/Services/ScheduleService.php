<?php

namespace App\Services;

use App\Models\Schedule;

class ScheduleService
{
    /**
     * Elimina los turnos (schedules) de un empleado a partir de una fecha dada.
     *
     * @param int    $employeeId ID del empleado.
     * @param string $date       Fecha desde la cual se eliminan los turnos (formato Y-m-d).
     * @return int Número de registros eliminados.
     */
    public function deleteSchedulesFromDate($employeeId, $date)
    {
        return Schedule::where('employee_id', $employeeId)
            ->whereDate('date', '>', $date)
            ->delete();
    }

    /**
     * Elimina los turnos (schedules) de un empleado dentro de un rango de fechas.
     * Se usa al suspender servicios por ausencia (vacación/reposo).
     *
     * @param int    $employeeId ID del empleado.
     * @param string $start      Fecha inicial (inclusive, Y-m-d).
     * @param string $end        Fecha final (inclusive, Y-m-d).
     * @return int Número de registros eliminados.
     */
    public function deleteSchedulesInRange($employeeId, $start, $end)
    {
        return Schedule::where('employee_id', $employeeId)
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end)
            ->delete();
    }
}
