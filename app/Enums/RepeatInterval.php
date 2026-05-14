<?php

namespace App\Enums;

enum RepeatInterval: string
{
    case WEEKLY = 'WEEKLY';
    case WEEKLY_2 = 'WEEKLY_2';
    case MONTHLY = 'MONTHLY';
    case YEARLY = 'YEARLY';

    // Definir próxima fecha
    public function addInterval($date)
    {
        return match($this) {
            self::WEEKLY => $date->addWeek(),
            self::WEEKLY_2 => $date->addWeeks(2),
            self::MONTHLY => $date->addMonth(),
            self::YEARLY => $date->addYear(),
        };
    }

    // Traducciones
    public function translateName(): string
    {
        return match($this) {
            self::WEEKLY => 'Semanal',
            self::WEEKLY_2 => 'Quincenal',
            self::MONTHLY => 'Mensual',
            self::YEARLY => 'Anual',
        };
    }

    public function label(): string
    {
        return match($this) {
            self::WEEKLY => 'una semana',
            self::WEEKLY_2 => 'dos semanas',
            self::MONTHLY => 'un mes',
            self::YEARLY => 'un año',
        };
    }

}