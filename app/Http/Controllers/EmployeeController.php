<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Assign;
use App\Models\Locker;
use App\Models\User;
use App\Models\EmergencyContact;
use App\Models\RoleSnapshot;
use App\Models\EmployeePeriod;

use Illuminate\Http\Request;
use App\Http\Resources\EmployeeResource;
use App\Http\Requests\StoreEmployeeRequest;
use Illuminate\Support\Facades\DB;
use App\Enums\LockerStatus;
use App\Services\LockerService;
use App\Services\RoleSnapshotService;

class EmployeeController extends Controller
{
    protected $lockerService;
    protected $roleSnapshotService;

    public function __construct(LockerService $lockerService, RoleSnapshotService $roleSnapshotService)
    {
        $this->lockerService = $lockerService;
        $this->roleSnapshotService = $roleSnapshotService;
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
            'roleSnapshot',
            'user.permissions',
            'user.roles.permissions',
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

            if ($data['use_meru_link']) {
                $user = User::create([
                    'username' => $data['username'],
                    'password' => $data['password'],
                    'change_pass_next_login' => $data['change_pass_next_login'] ?? false,
                ]);

                $employee->user_id = $user->id;
                $employee->save();

                // Guardar role_snapshot si se asignó un rol
                if (!empty($data['role_id'])) {
                    $this->roleSnapshotService->save($employee, $data['role_id'], $data['permissions'] ?? []);
                }
            }

            EmployeePeriod::create([
                'employee_id' => $employee->id,
                'hire_date' => $data['hire_date'],
            ]);

            return new EmployeeResource($employee->load([
                'position.department', 
                'position.subDepartment',
                'assignment',
                'roleSnapshot',
                'user.permissions',
                'user.roles.permissions',
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
                    'username'                   => $data['username'],
                    'status'                     => true,
                    'change_pass_next_login'     => $data['change_pass_next_login'] ?? false,
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
                    $currentUser = $user;
                }

                // Actualizar role_snapshot si se asignó un rol
                if (!empty($data['role_id'])) {
                    $this->roleSnapshotService->save($employee, $data['role_id'], $data['permissions'] ?? [], isUpdate: true);
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
                'roleSnapshot',
                'user.permissions',
                'user.roles.permissions',
            ]));
        });

    }

    public function changeStatus(Request $request, Employee $employee)
    {
        $field = $request->query('field');

        if (!$field) {
            return response()->json(['message' => 'El campo a cambiar es requerido.'], 400);
        }

        return DB::transaction(function () use ($field, $employee, $request) {
            
            $employeeDataReset = [];
            if ($field === 'status') {

                if (!$employee->status === false) {
                    $employeeDataReset = [
                        "use_meru_link" => false,
                        "use_locker" => false,
                        "use_hid_card" => false,
                        "use_transport" => false,
                    ];
                    $this->lockerService->unassignLocker($employee->id);
                    $employee->user?->update(['status' => false]);

                    // Guardar los datos de baja en el periodo laboral vigente
                    $this->saveRetireData($employee, $request);
                } else {
                    // Al reactivar, limpiar los datos de baja para que vuelva a contar como activo
                    $this->clearRetireData($employee);
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
     * Devuelve empleados que tienen asignado un permiso específico (vía role_snapshots).
     */
    public function byPermission(Request $request)
    {
        $permission = $request->query('permission');
        // $roleId = $request->query('selectedRole');

        if (!$permission) {// && !$roleId
            return response()->json([
                'message' => 'Debe proporcionar al menos uno de los parámetros: permission o selectedRole.',
            ], 400);
        }

        $query = RoleSnapshot::query();

        if ($permission) {
            $query->whereJsonContains('permissions', $permission);
        }

        // if ($roleId) {
        //     $query->where('role_id', $roleId);
        // }

        $snapshots = $query->with(['employee' => fn($q) => $q->select('id', 'first_name', 'last_name', 'position_id')
                ->with(['position' => fn($q) => $q->select('id', 'name', 'department_id')
                    ->with(['department' => fn($q) => $q->select('id', 'name')])
                ])
            ])
            ->get();

        return response()->json([
            'data' => $snapshots->map(fn($e) => [
                'id'         => $e->employee->id,
                'name'       => "{$e->employee->first_name} {$e->employee->last_name}",
                'department' => $e->employee->position?->department?->name ?? '—',
                'position'   => $e->employee->position?->name ?? '—',
                'roleName'       => $e->role_name ?? '—',
            ]),
        ]);
    }


    public function resetPass(Employee $employee)
    {
        $user = $employee->user;

        if (!$user) {
            return response()->json([
                'message' => 'Este empleado no tiene un usuario de MeruLink asociado.',
            ], 400);
        }

        $user->update([
            'password'               => $employee->ci,
            'change_pass_next_login' => true,
        ]);

        $employee->load(['user.roles', 'roleSnapshot']);

        return response()->json([
            'message' => "Contraseña restablecida a la cédula ({$employee->ci}). El usuario deberá cambiarla en el próximo inicio de sesión.",
            'data'    => new EmployeeResource($employee),
        ]);
    }

    /**
     * Guarda los datos de baja (tipo de egreso, fecha de efectividad y motivo)
     * en el periodo laboral vigente del empleado.
     */
    private function saveRetireData(Employee $employee, Request $request): void
    {
        $retireReason = $request->input('retire_reason');
        $retireDate   = $request->input('retire_date');
        $notes        = $request->input('notes');

        if (!$retireReason && !$retireDate && !$notes) {
            return;
        }

        $period = EmployeePeriod::where('employee_id', $employee->id)
            ->whereNull('retire_date')
            ->latest('id')
            ->first();

        if (!$period) {
            $period = EmployeePeriod::create([
                'employee_id' => $employee->id,
                'hire_date'   => $employee->hire_date ?? now()->toDateString(),
            ]);
        }

        $period->update([
            'retire_date'   => $retireDate ?? now()->toDateString(),
            'retire_reason' => $retireReason,
            'notes'         => $notes,
        ]);
    }

    /**
     * Limpia los datos de baja del periodo laboral cuando el empleado se reactiva.
     */
    private function clearRetireData(Employee $employee): void
    {
        $period = EmployeePeriod::where('employee_id', $employee->id)
            ->whereNotNull('retire_date')
            ->latest('id')
            ->first();

        if ($period) {
            $period->update([
                'retire_date'   => null,
                'retire_reason' => null,
                'notes'         => null,
            ]);
        }
    }
}
