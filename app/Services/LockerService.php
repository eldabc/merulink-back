<?php

namespace App\Services;

use App\Models\Assign;
use App\Models\Locker;
use Illuminate\Support\Facades\DB;
use App\Enums\LockerStatus;

class LockerService {
    /**
     * Lógica centralizada para asignar un locker a un empleado.
    */
    public function assignLocker($employeeId, $lockerAssignId)
    {
        return DB::transaction(function () use ($employeeId, $lockerAssignId) {
            
            // Revisa si ya hay locker asignado y libera
            $this->unassignLocker($employeeId);

            // Asigna
            $newAssign = Assign::findOrFail($lockerAssignId);
            if ($newAssign) {
                $newAssign->update([
                    'assign_code' => 'ASG' . $newAssign->locker->code . '-' . now()->format('d-m-Y'),
                    'assign_date' => now()->format('Y-m-d'),
                    'employee_id' => $employeeId,
                ]);

                Locker::where('id', $newAssign->locker_id)->update([
                    'status' => LockerStatus::OCCUPIED
                ]);

                return $newAssign;
            }
            return null;

            
        });
    }

    /**
     * Lógica para desasignar (quitarle el locker al empleado)
     */
    public function unassignLocker($employeeId)
    {
        $assignment = Assign::where('employee_id', $employeeId)->first();

        if ($assignment) {
            $assignment->update([
                'assign_date' => null,
                'assign_code' => '',
                'employee_id' => null,
            ]);

            Locker::where('id', $assignment->locker_id)->update([
                'status' => LockerStatus::MATCHED
            ]);
            return $assignment;
        }
        return null;
    }
}