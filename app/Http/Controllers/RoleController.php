<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Devuelve todos los roles (excepto super-admin) con sus permisos.
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

        return response()->json(['data' => $roles]);
    }
}
