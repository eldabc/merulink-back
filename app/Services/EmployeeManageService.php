<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeePeriod;

class EmployeeManageService
{
    protected $lockerService;

    public function __construct(LockerService $lockerService)
    {
        $this->lockerService = $lockerService;
    }

    /**
     * Desactiva de forma inmediata a un empleado (baja inmediata).
     *
     * @param array $data Campos esperados: effective_date, retire_reason, notes.
     */
    public function deactivate(Employee $employee, array $data): void
    {
        $employee->update([
            'status'        => false,
            'use_meru_link' => false,
            'use_locker'    => false,
            'use_hid_card'  => false,
            'use_transport' => false,
        ]);

        $this->lockerService->unassignLocker($employee->id);
        $this->saveEmployeePeriod($employee, $data);
        $employee->user?->update(['status' => false]);
    }

    /**
     * Desactivación programada de empleado.
     *
     * @param array $data Campos esperados: effective_date, retire_reason, notes.
     */
    public function scheduleDeactivate(Employee $employee, array $data): void
    {
        $this->saveEmployeePeriod($employee, $data, true);
    }

    /**
     * Reactiva a un empleado.
     *
     * @param array $data Campos esperados: effective_date.
     */
    public function reactivate(Employee $employee, array $data): void
    {
        $this->saveEmployeePeriod($employee, $data);
        $employee->update(['status' => true]);
    }

    /**
     * Guarda los datos de baja (retire_reason, effective_date y notes)
     * o reactivación (effective_date).
     */
    public function saveEmployeePeriod(Employee $employee, array $data, bool $scheduledDeactivate = false): void
    {
        $effectiveDate = $data['effective_date'] ?? null;

        if (!$effectiveDate) {
            return;
        }

        $period = EmployeePeriod::where('employee_id', $employee->id)
            ->whereNull('retire_date')
            ->latest('id')
            ->first();

        // Baja programada
        if ($period && $scheduledDeactivate) {
            $period->update([
                'scheduled_deactivate_date' => $effectiveDate,
                'retire_reason'             => $data['retire_reason'],
                'notes'                     => $data['notes'],
            ]);
            return;
        }

        // Sin periodo vigente: reactivación
        if (!$period) {
            $period = EmployeePeriod::create([
                'employee_id' => $employee->id,
                'hire_date'   => $effectiveDate,
            ]);
            return;
        }

        // Baja inmediata
        $period->update([
            'retire_date'               => $effectiveDate,
            'retire_reason'             => $data['retire_reason'],
            'notes'                     => $data['notes'],
        ]);
    }
}
