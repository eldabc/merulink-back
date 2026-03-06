<?php

namespace App\Enums;

enum PadlockStatus: string
{
    case AVAILABLE = 'Disponible';
    case ASSIGNED = 'Asignado';
    
    public function isAvailable(): bool
    {
        return $this === self::AVAILABLE;
    }

    public function isAssigned(): bool
    {
        return $this === self::ASSIGNED;
    }
}
