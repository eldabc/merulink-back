<?php

namespace App\Observers;

use App\Models\Position;
use App\Support\DepartmentCache;

class PositionObserver
{
    /**
     * Al crear/actualizar un cargo se invalida la caché de departamentos,
     * porque el DepartmentResource incluye positions en su JSON.
     */
    public function saved(Position $position): void
    {
        DepartmentCache::clear();
    }

    /**
     * Al eliminar un cargo se invalida la caché de departamentos.
     */
    public function deleted(Position $position): void
    {
        DepartmentCache::clear();
    }
}
