<?php

namespace App\Services;

class MenuService
{
    /**
     * Devuelve el menú visible para un usuario según sus permisos.
     *
     * Reglas:
     * - `hidden: true`          → siempre excluido (módulo no desarrollado), junto a sus descendientes.
     * - Hoja (sin hijos)        → visible si no exige permiso o el usuario lo tiene.
     * - Contenedor (con hijos)  → visible si el usuario tiene su propio permiso
     *                             O al menos uno de sus hijos es visible.
     *
     * Esto cumple la regla del menú superior: un módulo se muestra si el usuario
     * tiene el permiso del módulo (ej: view-employees) o cualquier permiso que
     * haga visible alguno de sus botones del sidebar.
     *
     * @param  array  $userPermissions  Array de permisos del usuario (strings).
     * @return array  Menú anidado filtrado, listo para serializar.
     */
    public function visibleFor(array $userPermissions): array
    {
        $perms = array_flip($userPermissions);

        $filter = function (array $items) use (&$filter, $perms): array {
            $visible = [];

            foreach ($items as $item) {
                // Módulo no desarrollado: se excluye junto a sus hijos
                if (!empty($item['hidden'])) {
                    continue;
                }

                $children = $item['children'] ?? [];
                $visibleChildren = !empty($children) ? $filter($children) : [];

                $hasOwnPermission = !empty($item['permission']);
                // El permiso propio solo "vale" si el item realmente exige uno y el usuario lo posee
                $ownOk = $hasOwnPermission && isset($perms[$item['permission']]);

                if (!empty($children)) {
                    // Contenedor: visible si el usuario tiene su propio permiso
                    // O al menos un hijo visible (por mérito propio).
                    // Un hijo SIN permiso no "otorga" visibilidad: solo es visible
                    // cuando su ancestro con permiso ya es visible.
                    if ($ownOk || count($visibleChildren) > 0) {
                        $item['children'] = $visibleChildren;
                        $visible[] = $item;
                    }
                } else {
                    // Hoja: sin permiso = siempre visible; con permiso = solo si lo posee
                    if (!$hasOwnPermission || $ownOk) {
                        $visible[] = $item;
                    }
                }
            }

            return $visible;
        };

        return $filter(config('menu', []));
    }
}
