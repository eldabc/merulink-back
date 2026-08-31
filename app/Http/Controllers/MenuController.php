<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponseHelper;
use App\Services\MenuService;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Menú del usuario autenticado, filtrado por sus permisos.
     *
     * El frontend solo renderiza lo que devuelve este endpoint; no filtra.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // admin: permisos del rol (Spatie); resto: permisos del role_snapshot
        $permissions = $user->username === 'admin'
            ? $user->getAllPermissions()->pluck('name')->values()->all()
            : ($user->employee?->roleSnapshot?->permissions ?? []);

        $menu = (new MenuService())->visibleFor($permissions);

        return ApiResponseHelper::createResponse('ok', 'menu_fetched', 'Menú obtenido', $menu);
    }
}
