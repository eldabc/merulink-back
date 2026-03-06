<?php

namespace App\Enums;

enum LockerStatus: string {
    
    case DISPONIBLE = 'Disponible';
    case OCUPADO = 'Ocupado';
    case MATCHED = 'Emparejado';
    
    public function isOccupied(): bool
    {
        return $this === self::OCUPADO;
    }

    public function isMatched(): bool
    {
        return $this === self::MATCHED;
    }
}

