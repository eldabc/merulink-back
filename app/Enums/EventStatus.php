<?php

namespace App\Enums;

enum EventStatus: string {
    
    case TENTATIVE = 'Tentativo';
    case CONFIRMED = 'Confirmado';
    
    public function isConfirmed(): bool
    {
        return $this === self::CONFIRMED;
    }

    public function isTentative(): bool
    {
        return $this === self::TENTATIVE;
    }
}

