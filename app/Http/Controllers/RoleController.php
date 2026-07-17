<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\PermissionHelper;

class RoleController extends Controller
{
    /**
     * Devuelve todos los roles (excepto super-admin) con sus permisos
     * ya formateados en estructura de tabla para el frontend.
     */
    public function index()
    {
        $roles = Role::where('name', '!=', 'super-admin')
            ->with('permissions')
            ->get()
            ->map(fn($role) => [
                'value'      => $role->id,
                'label'      => $role->name_label ?? ucfirst($role->name),
                'name'       => $role->name,
                'permissions'=> $role->permissions->pluck('name'),
            ]);

        // Agregar estructura formateada a cada rol
        $roles = $roles->map(function ($role) {
            $table = PermissionHelper::buildTable($role['permissions']->toArray());
            $role['permissionModules'] = $table['modules'];
            $role['permissionSpecials'] = $table['specials'];
            return $role;
        });

        // Todos los permisos disponibles (para calcular "otros" en frontend)
        $all = PermissionHelper::allPermissions();

        return response()->json([
            'data' => $roles,
            'allModules'  => $all['modules'],
            'allSpecials' => $all['specials'],
        ]);
    }
}
