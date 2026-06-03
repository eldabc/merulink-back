<?php

namespace App\Enums;

enum SystemShift: string
{
    case FREE = 'S-0';
    case RETIREMENT = 'S-1';
    case VACATIONS = 'S-2';

    /**
     * Retorna la estructura completa del turno simulando un objeto de BD
     */
    public function getData(): array
    {
        return match($this) {
            self::FREE => [
                'id' => self::FREE->value,
                'code' => 'L',
                'letterShift' => 'L',
                'color' => '#535759',
                'description' => 'Día Libre',
                'nightShift' => null,
                'typeShift' => null,
                'checkInTime' => null,
                'checkOutTime' => null,
            ],
            self::RETIREMENT => [
                'id' => self::RETIREMENT->value,
                'code' => 'BAJA',
                'letterShift' => 'BAJA',
                'color' => '#233044',
                'description' => 'Personal dado de baja',
                'nightShift' => null,
                'typeShift' => null,
                'checkInTime' => null,
                'checkOutTime' => null,
            ],
            self::VACATIONS => [
                'id' => self::VACATIONS->value,
                'code' => 'VAC',
                'letterShift' => 'VAC',
                'color' => '#d0d5d6',
                'description' => 'Periodo de Vacaciones',
                'nightShift' => null,
                'typeShift' => null,
                'checkInTime' => null,
                'checkOutTime' => null,
            ],
        };
    }

    /**
     * Método de utilidad para obtener todos los turnos de sistema formateados
     */
    public function toArray(): array
    {
        return $this->getData();
    }
}