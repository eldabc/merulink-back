<?php

namespace App\Console\Commands;

use App\Enums\LockerStatus;
use App\Enums\PadlockStatus;
use App\Models\Assign;
use App\Models\EmergencyContact;
use App\Models\Employee;
use App\Models\EmployeePeriod;
use App\Models\History;
use App\Models\RoleSnapshot;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vacation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:delete {employee : ID del empleado a eliminar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina definitivamente un empleado desactivado y todos sus registros asociados.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $id = (int) $this->argument('employee');

        $employee = Employee::find($id);

        if (!$employee) {
            $this->error("Empleado con ID {$id} no encontrado.");
            return self::FAILURE;
        }

        // Guard: solo se pueden eliminar empleados ya desactivados desde el sistema
        if ($employee->status) {
            $this->error(
                "El empleado #{$employee->id} ({$employee->first_name} {$employee->last_name}) está ACTIVO. " .
                'Solo se pueden eliminar empleados desactivados desde el sistema.'
            );
            return self::FAILURE;
        }

        if (!$this->confirm(
            "¿Eliminar DEFINITIVAMENTE al empleado #{$employee->id} " .
            "({$employee->first_name} {$employee->last_name}, CI {$employee->ci}) " .
            'y TODOS sus registros (horarios, vacaciones, contactos, períodos, asignaciones y usuario)?'
        )) {
            $this->info('Operación cancelada.');
            return self::SUCCESS;
        }

        try {
            $this->deletePermanently($employee);

            $this->info(
                "✅ Empleado #{$employee->id} ({$employee->first_name} {$employee->last_name}) " .
                'eliminado definitivamente junto con sus registros asociados.'
            );

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error eliminando al empleado: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * LÓGICA TEMPORAL.
     *
     * Borrado en orden ascendente (los que "heredan" se eliminan primero) para no romper FKs:
     * historial -> horarios -> vacaciones -> contactos -> períodos -> snapshots ->
     * asignaciones (liberando locker/candado) -> usuario vinculado -> empleado.
     *
     */
    private function deletePermanently(Employee $employee): void
    {
        DB::transaction(function () use ($employee) {
            $employeeId = $employee->id;
            $userId = $employee->user_id;

            // 1) Historial polimórfico del empleado
            History::where('auditable_type', Employee::class)
                ->where('auditable_id', $employeeId)
                ->delete();

            // 2) Horarios
            Schedule::where('employee_id', $employeeId)->delete();

            // 3) Vacaciones
            Vacation::where('employee_id', $employeeId)->delete();

            // 4) Contactos de emergencia
            EmergencyContact::where('employee_id', $employeeId)->delete();

            // 5) Períodos laborales
            $employee->employeePeriods()->delete();

            // 6) Snapshots de rol
            RoleSnapshot::where('employee_id', $employeeId)->delete();

            // 7) Asignaciones locker/candado: liberar y eliminar la fila
            $assigns = Assign::where('employee_id', $employeeId)->get();
            foreach ($assigns as $assign) {
                $assign->locker?->update(['status' => LockerStatus::AVAILABLE]);
                $assign->padlock?->update(['status' => PadlockStatus::AVAILABLE]);
                $assign->delete();
            }

            // 8) Usuario vinculado (si existe)
            if ($userId) {
                $user = User::find($userId);
                if ($user) {
                    // Historial del usuario y de registros que lo referencian
                    History::where(function ($q) use ($userId) {
                        $q->where('auditable_type', User::class)
                            ->where('auditable_id', $userId)
                            ->orWhere('user_id', $userId);
                    })->delete();

                    // Tokens de Sanctum y roles de Spatie
                    $user->tokens()->delete();
                    DB::table('model_has_roles')
                        ->where('model_id', $user->id)
                        ->where('model_type', User::class)
                        ->delete();

                    $user->delete();
                }
            }

            // 9) Empleado (por último)
            $employee->delete();
        });
    }
}
