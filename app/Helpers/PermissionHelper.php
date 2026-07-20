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

    /**
     * Acciones CRUD estándar.
     */
    public const CRUD_ACTIONS = ['create', 'view', 'edit', 'delete'];

    /**
     * Agrupa los permisos en una estructura lista para renderizar,
     * separando acciones CRUD (por módulo) de permisos especiales.
     *
     * @param  array  $permissionNames  Array de nombres de permisos
     * @return array{modules: array, specials: array}
     */
    public static function buildTable(array $permissionNames): array
    {
        $moduleLabels = __('permissions.modules');
        $modulesMap = [];

        // Clasificar: CRUD → módulo, especial → specials
        foreach ($permissionNames as $perm) {
            $lastDash = strrpos($perm, '-');
            if ($lastDash === false) continue;

            $action = substr($perm, 0, $lastDash);
            $moduleKey = substr($perm, $lastDash + 1);

            if (in_array($action, self::CRUD_ACTIONS, true)) {
                if (!isset($modulesMap[$moduleKey])) {
                    $modulesMap[$moduleKey] = [
                        'key'    => $moduleKey,
                        'label'  => $moduleLabels[$moduleKey] ?? $moduleKey,
                        'create' => false,
                        'view'   => false,
                        'edit'   => false,
                        'delete' => false,
                    ];
                }
                $modulesMap[$moduleKey][$action] = true;
            }
        }

        // Recolectar especiales
        $specials = [];
        foreach ($permissionNames as $perm) {
            $lastDash = strrpos($perm, '-');
            if ($lastDash === false) continue;

            $action = substr($perm, 0, $lastDash);
            if (!in_array($action, self::CRUD_ACTIONS, true)) {
                $specials[] = [
                    'key'   => $perm,
                    'label' => self::displayName($perm),
                ];
            }
        }

        return [
            'modules'  => array_values($modulesMap),
            'specials' => $specials,
        ];
    }

    /**
     * Devuelve la estructura de módulos desde las traducciones, rellena
     * solo con los permisos que realmente existen en la BD.
     * Los módulos sin ningún permiso en BD se excluyen.
     *
     * @return array{modules: array}
     */
    public static function allPermissions(): array
    {
        $moduleLabels = __('permissions.modules');
        $specialsLabels = __('permissions.specials');
        $dbPermissionNames = \Spatie\Permission\Models\Permission::pluck('name')->toArray();
        $dbPermissionSet = array_flip($dbPermissionNames);

        $modulesMap = [];

        // Recorrer módulos de traducciones y ver qué permisos existen en BD
        foreach ($moduleLabels as $moduleKey => $moduleLabel) {
            $row = [
                'key'      => $moduleKey,
                'label'    => $moduleLabel,
                'create'   => null,
                'view'     => null,
                'edit'     => null,
                'delete'   => null,
                'specials' => [],
            ];
            $hasAny = false;

            // CRUD: solo si el permiso existe en BD
            foreach (self::CRUD_ACTIONS as $action) {
                $permName = "{$action}-{$moduleKey}";
                if (isset($dbPermissionSet[$permName])) {
                    $row[$action] = $permName;
                    $hasAny = true;
                }
            }

            // Especiales del módulo: solo si existen en BD
            foreach ($specialsLabels as $permName => $label) {
                $lastDash = strrpos($permName, '-');
                if ($lastDash === false) continue;
                $spModuleKey = substr($permName, $lastDash + 1);

                if ($spModuleKey === $moduleKey && isset($dbPermissionSet[$permName])) {
                    $row['specials'][] = [
                        'key'   => $permName,
                        'label' => $label,
                    ];
                    $hasAny = true;
                }
            }

            if ($hasAny) {
                $modulesMap[$moduleKey] = $row;
            }
        }

        return [
            'modules'  => array_values($modulesMap),
        ];
    }
}
