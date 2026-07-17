<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\RoleSnapshot;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;

class RoleSnapshotService
{
    /**
     * Guarda o actualiza el snapshot de rol para un empleado.
     * En modo update elimina el anterior y crea uno nuevo.
     *
     * @param  Employee  $employee  El empleado
     * @param  int       $roleId    ID del rol asignado
     * @param  array     $permissions  Array de nombres de permisos
     * @param  bool      $isUpdate  Si es true, elimina el snapshot anterior primero
     * @return RoleSnapshot
     */
    public static function save(Employee $employee, int $roleId, array $permissions = [], bool $isUpdate = false): RoleSnapshot
    {
        $role = Role::find($roleId);
        $roleName = $role?->name_label ?? $role?->name ?? 'Sin rol';

        if ($isUpdate) {
            $employee->roleSnapshot()->delete();
        }

        return $employee->roleSnapshot()->create([
            'role_id'     => $roleId,
            'role_name'   => $roleName,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Sincroniza los permisos reales en Spatie (para middlewares).
     */
    public static function syncSpatie(Model $user, int $roleId, array $permissions = []): void
    {
        $role = Role::find($roleId);

        if ($role) {
            $user->syncRoles([$role->name]);
            $user->syncPermissions($permissions);
        }
    }
}
