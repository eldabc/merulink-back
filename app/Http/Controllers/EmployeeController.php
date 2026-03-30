<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Assign;
use App\Models\Locker;
use App\Models\EmergencyContact;

use Illuminate\Http\Request;
use App\Http\Resources\EmployeeResource;
use App\Http\Requests\StoreEmployeeRequest;
use Illuminate\Support\Facades\DB;
use App\Enums\LockerStatus;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Employee::query();

        // Filtro por sexo (H o M)
        if ($request->has('sex')) {
            $query->where('sex', $request->sex);
        }

        // Filtro empleados SIN asignación activa
        if ($request->boolean('unassigned')) {
            $query->whereDoesntHave('assignment'); 
        }

        $employees = $query->with([
            'position.department', 
            'position.subDepartment',
            'assignment'
        ])->get();

        return EmployeeResource::collection($employees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();
        return DB::transaction(function () use ($data) {
            // return response()->json([ 'data' => $data, 'dataDos' => $data['assign_id'] ]);

            // Crear
            $employee = Employee::create($data);

            if (isset($data['assign_id'])) {
                $assignment = Assign::find($data['assign_id']);

                if ($assignment) {
                    $assignment->update([
                        'assign_code' => 'ASG' . $assignment->locker->code . '-' . now()->format('d-m-Y'),
                        'assign_date' => now()->format('Y-m-d'),
                        'employee_id' => $employee->id,
                    ]);

                    Locker::where('id', $assignment->locker_id)->update([
                        'status' => LockerStatus::OCCUPIED
                    ]);
                }
            }

            if (isset($data['contacts'])) {
                foreach ($data['contacts'] as $contact) {
                    $employee->emergencyContacts()->create($contact);
                }
            }

            return new EmployeeResource($employee->load([
                'position.department', 
                'position.subDepartment',
                'assignment'
            ]));
        });

    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreEmployeeRequest $request, Employee $employee)
    {
        $data = $request->validated();
        return DB::transaction(function () use ($data, $employee) {

            $employee->update($data);

            $assignment = Assign::where('employee_id', $employee->id)->first();
            if (isset($data['assign_id'])) {
                if (isset($assignment) && $assignment?->id !== $data['assign_id']) {
                    $assignment->update([
                        'assign_code' => '',
                        'assign_date' => null,
                        'employee_id' => null,
                    ]);

                    Locker::where('id', $assignment->locker_id)->update([ 'status' => LockerStatus::MATCHED ]);

                    $assign = Assign::find($data['assign_id']);

                    if ($assign) {
                        $assign->update([
                            'assign_code' => 'ASG' . $assign->locker->code . '-' . now()->format('d-m-Y'),
                            'assign_date' => now()->format('Y-m-d'),
                            'employee_id' => $employee->id,
                        ]);

                        Locker::where('id', $assign->locker_id)->update([
                            'status' => LockerStatus::OCCUPIED
                        ]);
                    }
                }

            } elseif ($assignment)  {
                $assignment->delete();
            }

            $contacts = EmergencyContact::where('employee_id', $employee->id)->get();

            if ($contacts->count() > 0) {
                $employee->emergencyContacts()->delete();
            }

            if (isset($data['contacts'])) {
                foreach ($data['contacts'] as $contact) {
                    $employee->emergencyContacts()->create($contact);
                }
            }


            return new EmployeeResource($employee->load([
                'position.department', 
                'position.subDepartment',
                'assignment'
            ]));
        });

    }

    public function changeStatus(Request $request, Employee $employee)
    {
        $field = $request->query('field');

        if (!$field) {
            return response()->json(['message' => 'El campo a cambiar es requerido.'], 400);
        }
        
        return response()->json([ 'employee' => $employee, 'field' => $field ]);

        DB::transaction(function () use ($field, $employee) {
            
            // $newStatus = !$field;
            $employeeDataReset = [];
            if ($field === 'status') {
                $newStatus = !$employee->status;
                // updatedEmployee.status = $newStatus;   
                if ($newStatus === false) {
                    $employeeDataReset = [
                        use_meru_link => false,
                        // user_name => '',
                        // user_pass => '',
                        use_locker => false,
                        assign => null,
                        use_hid_card => false,
                        use_transport => false,
                    ];
                }       
            } 
            // else {
            //     // updatedEmployee[field] = !emp[field];
            //     $employee->$field = !$employee->$field;
            // }
            if ($field === 'use_locker') {
                // Se resetea la asignación (crear servicio para esto)
                // Se cambia el status de locker a MATCHED y padlock a AVAILABLE
                // assign => null,
            }

            $employee->update([
                $field => !$employee->$field,
                $employeeDataReset
            ]);
            // $employee->update([
            //     $field => $newStatus,
            //     $employeeDataReset
            // ]);
            
            $assigns = Assign::whereHas('locker.lockerCategory', function ($q) use ($id) {
                $q->where('key', $id);
            })->get();

            foreach ($assigns as $assign) {
                // Método privado para resetear los estados
                $this->resetStatuses($assign);
                $assign->delete();
            }
        });

        // Para front, status 204 es suficiente confirmación
        return response()->noContent(); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        //
    }
}
