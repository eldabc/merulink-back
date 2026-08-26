<?php

namespace App\Enums;

enum SystemShift: string
{
    case FREE = 'S-0';
    case RETIREMENT = 'S-1';
    case VACATIONS = 'S-2';
    case PERMISSION = 'S-3';

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
                'description' => 'Libre',
                'nightShift' => null,
                'typeShift' => null,
                'checkInTime' => null,
                'checkOutTime' => null,
                'isSystemShift' => true,
                'isNotShowShift' => false
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
                'isSystemShift' => true,
                'isNotShowShift' => true
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
                'isSystemShift' => true,
                'isNotShowShift' => true
            ],
            self::PERMISSION => [
                'id' => self::PERMISSION->value,
                'code' => 'PER',
                'letterShift' => 'PER',
                'color' => '#00ffd7',
                'description' => 'Permiso médico (reposo)',
                'nightShift' => null,
                'typeShift' => null,
                'checkInTime' => null,
                'checkOutTime' => null,
                'isSystemShift' => true,
                'isNotShowShift' => true
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