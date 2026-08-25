<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class DepartmentCache
{
    /**
     * Cache key usada para el listado de departamentos.
     *
     * El DepartmentResource incluye subDepartments y positions en su JSON,
     * por eso esta caché debe invalidarse también cuando cambian esos modelos.
     */
    public const KEY = 'departments.all';

    /**
     * Invalida la caché de departamentos.
     */
    public static function clear(): void
    {
        Cache::forget(self::KEY);
    }
}
