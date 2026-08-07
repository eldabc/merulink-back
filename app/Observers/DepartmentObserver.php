<?php

namespace App\Observers;

use App\Models\Department;
use Illuminate\Support\Facades\Cache;

class DepartmentObserver
{
    /**
     * Cache key usada para el listado de departamentos.
     */
    private const CACHE_KEY = 'departments.all';

    /**
     * Limpia la caché al crear/actualizar.
     */
    public function saved(Department $department): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Limpia la caché al eliminar.
     */
    public function deleted(Department $department): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
