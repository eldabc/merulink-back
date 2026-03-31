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
use App\Services\LockerService;

class EmployeeController extends Controller
{
    protected $lockerService;

    public function __construct(LockerService $lockerService)
    {
        $this->lockerService = $lockerService;
    }

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

            $employee = Employee::create($data);

            if (isset($data['assign_id'])) {
                $assignment = Assign::find($data['assign_id']);

                if ($assignment) {
                    $this->lockerService->assignLocker($employee->id, $assignment->id);
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

            $assignId = $data['assign_id'] ?? null;
            $currentAssign = $employee->assignment;

            if ($assignId) {
                if (!$currentAssign || $currentAssign->id !== $assignId) {
                    $this->lockerService->assignLocker($employee->id, $assignId);
                }
            } elseif ($currentAssign) {
                $this->lockerService->unassignLocker($employee->id);
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

        return DB::transaction(function () use ($field, $employee) {
            
            $employeeDataReset = [];
            if ($field === 'status') {
                $newStatus = !$employee->status;

                if (!$employee->status === false) {
                    $employeeDataReset = [
                        "use_meru_link" => false,
                        "use_locker" => false,
                        "use_hid_card" => false,
                        "use_transport" => false,
                    ];
                    $this->lockerService->unassignLocker($employee->id);
                }       
            } 

            if ($field === 'use_locker' && !$employee->use_locker) {
                $this->lockerService->unassignLocker($employee->id);
            }

            $employee->update(array_merge(
                [$field => !$employee->$field],
                $employeeDataReset
            ));
            
            return new EmployeeResource($employee->fresh()->load([
                'position.department', 
                'position.subDepartment',
                'assignment'
            ]));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        //
    }
}
