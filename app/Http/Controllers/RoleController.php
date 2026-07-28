<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\PermissionHelper;
use App\Helpers\ApiResponseHelper;
use App\Helpers\StringHelper;
use App\Models\RoleSnapshot;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
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
                'id'             => $role->id,
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

    public function show(Role $role)
    {
        $role->load('permissions');

        return ApiResponseHelper::createResponse(
            'ok',
            'role_found',
            'Rol encontrado',
            new RoleResource($role)
        );
    }

    public function store(RoleRequest $request)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data) {
            $normalizedName = StringHelper::slugify($data['role_name']);

            $role = Role::create([
                'name'       => $normalizedName,
                'name_label' => trim($data['role_name']),
                'guard_name' => 'sanctum',
            ]);

            // Sincronizar permisos
            $role->syncPermissions($data['permissions']);
            $role->load('permissions');
            $role->forgetCachedPermissions();

            return ApiResponseHelper::createResponse(
                'ok',
                'created_role',
                'Rol creado exitosamente'
            );
        });
    }

    public function update(RoleRequest $request, Role $role)
    {
        $data = $request->validated();

        return DB::transaction(function () use ($data, $role) {

            $role->update([ 'name_label' => trim($data['role_name']) ]);

            // Elimina los permisos desmarcados, añade los nuevos
            $role->syncPermissions($data['permissions']);
            $role->load('permissions');
            $role->forgetCachedPermissions();

            return ApiResponseHelper::createResponse(
                'ok',
                'updated_role',
                'Rol actualizado exitosamente',
                new RoleResource($role)
            );
        });
    }

    public function destroy(Role $role)
    {
        // if (RoleSnapshot::where('role_id', $role->id)->exists()) {
        //     return ApiResponseHelper::createResponse(
        //         'fail',
        //         'role_has_employees',
        //         'No se puede eliminar el rol: tiene empleados asociados.',
        //         null,
        //         422
        //     );
        // }

        // return DB::transaction(function () use ($role) {
        //     // Limpiar permisos antes de borrar el rol
        //     $role->syncPermissions([]);
        //     $role->forgetCachedPermissions();

        //     $role->delete();

        //     return ApiResponseHelper::createResponse(
        //         'ok',
        //         'deleted_role',
        //         'Rol eliminado exitosamente.'
        //     );
        // });
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
    public function getRolesPermissions(Request $request)
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

        $allRolePermissions = $basePermissions;
        if($request->boolean('getAssignments')) {
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
        }       

        $roles = Role::where('name', '!=', 'super-admin')
            ->orderBy('name_label')
            ->get()
            ->map(function ($role) use ($counts, $allRolePermissions) {
                $perms = $allRolePermissions[$role->id] ?? [];
                return [
                    'id'               => $role->id,
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
