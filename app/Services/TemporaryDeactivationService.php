<?php

namespace App\Services;

use App\Models\Vacation;
use Carbon\Carbon;

/**
 * Desactivación TEMPORAL de servicios durante una ausencia (vacación/reposo).
 *
 * Este servicio NO toca el contrato: el empleado sigue con su employee_period
 * abierto, solo se apagan sus servicios mientras dura la ausencia y se restauran al volver.
 *
 * Diseño:
 * - Al suspender se guarda un snapshot en JSON (prev_services) para restaurar el estado correcto al reanudar.
 * - `services_suspended_at` / `services_restore_at` hacen el sync idempotente.
 */
class TemporaryDeactivationService
{
    public function __construct(private ScheduleService $scheduleService)
    {
    }

    /**
     * Sincroniza el estado operativo de TODOS los empleados según sus ausencias.
     *
     * @return array{0: int, suspended: int, restored: int, cleaned: int} Contadores de acciones aplicadas.
     */
    public function sync(?string $today = null): array
    {
        $today = $today ? Carbon::parse($today) : Carbon::today();

        $stats = ['suspended' => 0, 'restored' => 0, 'cleaned' => 0];

        Vacation::with('employee.user')
            ->get()
            ->each(function (Vacation $vacation) use ($today, &$stats) {
                foreach ($this->syncVacation($vacation, $today) as $action) {
                    $stats[$action] = ($stats[$action] ?? 0) + 1;
                }
            });

        return $stats;
    }

    /**
     * Aplica o anula la desactivación temporal de un empleado según la fecha.
     *
     * @return array Lista de acciones aplicadas: 'suspended' | 'restored' | 'cleaned'.
     */
    public function syncVacation(Vacation $vacation, ?Carbon $today = null): array
    {
        $actions = [];

        $today = $today ?: Carbon::today();
        $start = Carbon::parse($vacation->start);
        $end = Carbon::parse($vacation->end);

        if ($start->lte($today) && $end->gte($today)) {
            // Ausencia activa hoy (aunque haya empezado antes) → suspender servicios.
            // Solo se cuenta como acción si realmente suspendió (idempotente).
            if ($this->suspend($vacation)) {
                $actions[] = 'suspended';
            }
        } else {
            // Ausencia ya terminada → limpieza retroactiva de turnos en el rango
            // (sin tocar servicios: es pasado, no afecta el estado actual).
            if ($end->lt($today)) {
                $deleted = $this->scheduleService->deleteSchedulesInRange($vacation->employee_id, $vacation->start, $vacation->end);
                if ($deleted > 0) {
                    $actions[] = 'cleaned';
                }
            }

            // Si estaba suspendida (porque terminó o aún no empieza) → reanudar servicios
            if ($this->restore($vacation)) {
                $actions[] = 'restored';
            }
        }

        return $actions;
    }

    /**
     * Suspende temporalmente los servicios del empleado mientras dura la ausencia.
     * NO libera locker/candado (se mantienen asignados).
     *
     * @return bool true si realmente suspendió; false si ya estaba suspendida (idempotente).
     */
    public function suspend(Vacation $vacation): bool
    {
        // Idempotente: si ya está suspendida (y no fue restaurada), no repetir.
        if ($this->isSuspended($vacation) && !$vacation->services_restore_at) {
            return false;
        }

        $employee = $vacation->employee;
        if (!$employee) {
            return false;
        }

        // Snapshot del estado previo en JSON (los servicios pueden crecer sin migrar)
        $vacation->update([
            'prev_services'         => [
                'use_meru_link' => $employee->use_meru_link,
                'use_hid_card'  => $employee->use_hid_card,
                'use_transport' => $employee->use_transport,
                'user_status'   => $employee->user?->status,
            ],
            'services_suspended_at' => now(),
            'services_restore_at'   => null,
        ]);

        // Apagar servicios de acceso
        $employee->update([
            'use_meru_link' => false,
            'use_hid_card'  => false,
            'use_transport' => false,
        ]);
        $employee->user?->update(['status' => false]);

        // Quitar turnos registrados durante la ausencia
        $this->scheduleService->deleteSchedulesInRange($employee->id, $vacation->start, $vacation->end);

        return true;
    }

    /**
     * Restaura los servicios del empleado al terminar la ausencia.
     *
     * @return bool true si realmente restauró; false si no estaba suspendida o ya fue restaurada.
     */
    public function restore(Vacation $vacation): bool
    {
        if (!$this->isSuspended($vacation) || $vacation->services_restore_at) {
            return false; // no estaba suspendida o ya fue restaurada
        }

        $employee = $vacation->employee;
        if (!$employee) {
            return false;
        }

        $prev = $vacation->prev_services ?? [];

        $employee->update([
            'use_meru_link' => $prev['use_meru_link'] ?? true,
            'use_hid_card'  => $prev['use_hid_card'] ?? true,
            'use_transport' => $prev['use_transport'] ?? true,
        ]);

        // Restaurar el usuario solo si había uno vinculado al momento de suspender
        if ($employee->user) {
            $employee->user->update(['status' => $prev['user_status'] ?? true]);
        }

        $vacation->update(['services_restore_at' => now()]);

        return true;
    }

    private function isSuspended(Vacation $vacation): bool
    {
        return $vacation->services_suspended_at !== null;
    }
}
