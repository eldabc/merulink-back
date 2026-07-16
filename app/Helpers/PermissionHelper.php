<?php

namespace App\Helpers;

class PermissionHelper
{
    /**
     * Resuelve el nombre legible de un permiso.
     * Formato requerido "{action}-{module}"
     * Ejemplos:
     *   'create-schedules'   → 'Crear Horarios'
     *   'view-employees'     → 'Ver Empleados'
     *   'reviewed-schedules' → 'Revisar horarios'  (en specials)
     *
     * @param  string  $permission  Nombre del permiso (ej: "create-schedules")
     * @return string  Etiqueta legible o el nombre original si no hay traducción
     */
    public static function displayName(string $permission): string
    {
        // Buscar si es un permiso especial con traducción exacta
        $specialLabel = __("permissions.specials.{$permission}");
        if ($specialLabel !== "permissions.specials.{$permission}") {
            return $specialLabel;
        }

        // Parsear acción + módulo
        $lastDash = strrpos($permission, '-');
        if ($lastDash === false) {
            return $permission;
        }

        $action = substr($permission, 0, $lastDash);
        $module = substr($permission, $lastDash + 1);

        $actionLabel = __("permissions.actions.{$action}");
        $moduleLabel = __("permissions.modules.{$module}");

        // Si no encontró traducción para la acción, usar el nombre original
        if (str_starts_with($actionLabel, 'permissions.actions.')) {
            $actionLabel = $action;
        }

        // Si no encontró traducción para el módulo, usar el nombre original
        if (str_starts_with($moduleLabel, 'permissions.modules.')) {
            $moduleLabel = $module;
        }

        return "{$actionLabel} {$moduleLabel}";
    }

    /**
     * Resuelve los nombres legibles de múltiples permisos.
     *
     * @param  array  $permissions  Array de nombres de permisos
     * @return array<string, string>  Mapa de nombre → label
     */
    public static function displayNames(array $permissions): array
    {
        $labels = [];
        foreach ($permissions as $perm) {
            $labels[$perm] = self::displayName($perm);
        }
        return $labels;
    }
}
