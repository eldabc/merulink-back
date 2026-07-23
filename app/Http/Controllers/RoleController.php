<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\PermissionHelper;
use App\Helpers\ApiResponseHelper;
use App\Models\RoleSnapshot;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;

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
                'value'          => $role->id,
                'label'          => $role->name_label ?? ucfirst($role->name),
                'name'           => $role->name,
                'permissions'    => $role->permissions->pluck('name'),
            ]);

        // Todos los permisos por módulo
        $all = PermissionHelper::allPermissions();

        return response()->json([
            'data' => $roles,
            'allModules'  => $all['modules'],
        ]);
    }

    public function store(RoleRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            // Normalizar: minúsculas, sin espacios, espacios - guiones
            $normalizedName = preg_replace('/\s+/', '-', trim(strtolower($data['role_name'])));

            $role = Role::create([
                'name'       => $normalizedName,
                'name_label' => trim($data['role_name']),
                'guard_name' => 'sanctum',
            ]);

            // Vincular permisos directamente en la tabla pivote (evita guard mismatch)
            $permissionIds = Permission::whereIn('name', $data['permissions'])->pluck('id');
            $role->permissions()->sync($permissionIds);
            $role->load('permissions');
            $role->forgetCachedPermissions();

            return ApiResponseHelper::createResponse(
                'ok',
                'created_role',
                'Rol creado exitosamente'
            );
        });
    }

   
    /**
     * Devuelve todos los permisos del sistema agrupados por módulo,
     * con key y label en español.
     */
    public function allPermissions()
    {
        $allPermissionNames = Permission::pluck('name')->toArray();

        return response()->json(
            PermissionHelper::buildGroupedPermissions($allPermissionNames)
        );
    }

    
    /**
     * Devuelve listado personalizado de 
     * roles - permisos agupados por módulo
     */
    public function getRolesPermissions()
    {
        // Contador de empleados asignados por rol
        $counts = RoleSnapshot::select('role_id', DB::raw('count(*) as total'))
            ->groupBy('role_id')
            ->pluck('total', 'role_id');

        // Permisos base del rol (Spatie)
        $basePermissions = Role::where('name', '!=', 'super-admin')
            ->with('permissions')
            ->get()
            ->mapWithKeys(fn($role) => [
                $role->id => $role->permissions->pluck('name')->toArray(),
            ]);

        // Todos los permisos únicos desde role_snapshots (versiones personalizadas)
        $snapshotPermissions = RoleSnapshot::select('role_id', 'permissions')
            ->get()
            ->groupBy('role_id')
            ->map(function ($snapshots) {
                return $snapshots->flatMap(fn($s) => $s->permissions ?? [])
                    ->unique()
                    ->values()
                    ->toArray();
            });

        // Unir: base del rol + versiones de snapshots
        $allRolePermissions = collect($basePermissions)->mapWithKeys(function ($perms, $roleId) use ($snapshotPermissions) {
            $merged = array_unique(array_merge($perms, $snapshotPermissions[$roleId] ?? []));
            return [$roleId => $merged];
        });

        $roles = Role::where('name', '!=', 'super-admin')
            ->get()
            ->map(function ($role) use ($counts, $allRolePermissions) {
                $perms = $allRolePermissions[$role->id] ?? [];
                return [
                    'value'            => $role->id,
                    'label'            => $role->name_label ?? ucfirst($role->name),
                    'name'             => $role->name,
                    'employeeCount'    => $counts[$role->id] ?? 0,
                    'permissionGroups' => PermissionHelper::buildGroupedPermissions($perms),
                ];
            });

        return response()->json([
            'data' => $roles,
        ]);
    }
}
