<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\RoleSnapshot;
use Spatie\Permission\Models\Role;

class RoleSnapshotService
{
    /**
     * Guarda o actualiza el snapshot de rol para un empleado.
     * En modo update elimina el anterior y crea uno nuevo.
     *
     * @param  Employee  $employee     El empleado
     * @param  int       $roleId       ID del rol asignado
     * @param  array     $permissions  Array de nombres de permisos
     * @param  bool      $isUpdate     Si es true, elimina el snapshot anterior primero
     * @param  array     $departments  Array de ids de departamentos a los que tiene acceso
     * @return RoleSnapshot
     */
    public function save(Employee $employee, int $roleId, array $permissions = [], bool $isUpdate = false, array $departments = []): RoleSnapshot
    {
        $role = Role::find($roleId);
        $roleName = $role?->name_label ?? $role?->name ?? 'Sin rol';

        if ($isUpdate) {
            $employee->roleSnapshot()->delete();
        }

        // Sincronizar el rol de Spatie en el usuario vinculado para que
        // getRoleNames()/hasRole() y el middleware `role` funcionen.
        if ($role && $employee->user_id) {
            $employee->user()->first()?->syncRoles([$roleId]);
        }

        return $employee->roleSnapshot()->create([
            'role_id'      => $roleId,
            'role_name'    => $roleName,
            'permissions'  => $permissions,
            'departments'  => $departments,
        ]);
    }

}
