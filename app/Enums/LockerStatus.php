<?php

namespace App\Enums;

enum LockerStatus: string {
    
    case AVAILABLE = 'Disponible';
    case OCCUPIED = 'Ocupado';
    case MATCHED = 'Emparejado';
    
    public function isOccupied(): bool
    {
        return $this === self::OCCUPIED;
    }

    public function isMatched(): bool
    {
        return $this === self::MATCHED;
    }
}

