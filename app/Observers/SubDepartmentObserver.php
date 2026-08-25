<?php

namespace App\Observers;

use App\Models\SubDepartment;
use App\Support\DepartmentCache;

class SubDepartmentObserver
{
    /**
     * Al crear/actualizar un subdepartamento se invalida la caché de
     * departamentos, porque el DepartmentResource lo incluye en su JSON.
     */
    public function saved(SubDepartment $subDepartment): void
    {
        DepartmentCache::clear();
    }

    /**
     * Al eliminar un subdepartamento se invalida la caché de departamentos.
     */
    public function deleted(SubDepartment $subDepartment): void
    {
        DepartmentCache::clear();
    }
}
