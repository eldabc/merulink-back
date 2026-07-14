<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Assign;
use App\Models\Locker;
use App\Models\User;
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
            'assignment',
            'user'
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

            if (filled($data['use_meru_link'])) {
                $user = User::create([
                    'name'     => trim($employee->first_name . ' ' . $employee->last_name),
                    'username' => $data['username'],
                    'email'    => $employee->email,
                    'password' => $data['password'],
                ]);

                $employee->user_id = $user->id;
                $employee->save();
            }

            return new EmployeeResource($employee->load([
                'position.department', 
                'position.subDepartment',
                'assignment',
                'user'
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

            // Sincronizar usuario del sistema
            $useMeruLink = $data['use_meru_link'] ?? false;
            $currentUser = $employee->user;

            if ($useMeruLink) {
                $userData = [
                    'name'     => trim($employee->first_name . ' ' . $employee->last_name),
                    'username' => $data['username'],
                    'email'    => $employee->email,
                    'status'   => true,
                ];

                if (!empty($data['password'])) {
                    $userData['password'] = $data['password'];
                }

                if ($currentUser) {
                    // Si no se envía contraseña nueva, no sobreescribir
                    if (empty($data['password'])) {
                        unset($userData['password']);
                    }
                    $currentUser->update($userData);
                } else {
                    $user = User::create($userData);
                    $employee->user_id = $user->id;
                    $employee->save();
                }
            } else {
                if ($currentUser) {
                    $currentUser->update(['status' => false]);
                }
            }

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
                'assignment',
                'user'
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
                // $newStatus = !$employee->status;

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

            if ($field === 'use_meru_link') {
                $employee->user?->update(['status' => !$employee->use_meru_link]);
            }

            $employee->update(array_merge(
                [$field => !$employee->$field],
                $employeeDataReset
            ));
            
            return new EmployeeResource($employee->fresh()->load([
                'position.department', 
                'position.subDepartment',
                'assignment',
                'user'
            ]));
        });
    }

    /**
     * Trae los empleados para schedule
     */
    // public function filterSchedule(Request $request)
    // {
    //     $query = Employee::query();
    //     $isFiltering = $request->filled('departmentId') && $request->filled('start') && $request->filled('end');

    //     // Filtro por ID de departamento para schedule
    //     if ($isFiltering){
    //         // Rango de la quincena
    //         $start = $request->start;
    //         $end = $request->end;

    //         $query->whereHas('position', function ($q) use ($request) {
    //             $q->where('department_id', $request->departmentId);
    //         });

    //         $query->where(function ($q) use ($start, $end) {
    //             // Empleados activos normales
    //             $q->where('status', true)
    //             // Empleados inactivos que están de vacaciones en el rango
    //             ->orWhere(function ($sub) use ($start, $end) {
    //                 $sub->where('status', false)
    //                     ->whereHas('vacations', function ($v) use ($start, $end) {
    //                         $v->where(function ($vQuery) use ($start, $end) {
    //                             $vQuery->whereBetween('start', [$start, $end])
    //                                     ->orWhereBetween('end', [$start, $end])
    //                                     ->orWhere(function ($deep) use ($start, $end) {
    //                                         $deep->where('start', '<=', $start) // Empezó antes o igual que la quincena
    //                                             ->where('end', '>=', $end); // Termina después o igual que la quincena
    //                                     });
    //                         });
    //                     });
    //             });
    //         });
    //     }

    //     $employees = $query->with([
    //         'position.department', 
    //         'position.subDepartment',
    //         // Filtro para traer solo si esta de vacaciones
    //         'vacations' => function ($q) use ($start, $end) {
    //             $q->where(function ($vQuery) use ($start, $end) {
    //                 $vQuery->whereBetween('start', [$start, $end])
    //                     ->orWhereBetween('end', [$start, $end])
    //                     ->orWhere(function ($deep) use ($start, $end) {
    //                         $deep->where('start', '<=', $start)
    //                                 ->where('end', '>=', $end);
    //                     });
    //             });
    //         }
    //     ])->get();

    //     // agrupar por subdepartamento
    //     if ($request->filled('departmentId')) {
    //         $groupedEmployees = $employees->groupBy(function ($employee) {
    //             // Fallback "Sin Subdepartamento" para la posición que no tiene asignado uno
    //             return $employee->position->subDepartment->name ?? 'Sin Subdepartamento';
    //         });

    //         // Retorna el map agrupado
    //         return response()->json(
    //             $groupedEmployees->map(function ($group) {
    //                 return EmployeeFilterScheduleResource::collection($group);
    //             })
    //         );
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        //
    }
}
