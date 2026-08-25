<?php

namespace App\Observers;

use App\Models\Department;
use App\Support\DepartmentCache;

class DepartmentObserver
{
    /**
     * Limpia la caché de departamentos al crear/actualizar.
     */
    public function saved(Department $department): void
    {
        DepartmentCache::clear();
    }

    /**
     * Limpia la caché de departamentos al eliminar.
     */
    public function deleted(Department $department): void
    {
        DepartmentCache::clear();
    }
}
