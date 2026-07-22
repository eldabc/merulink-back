<?php

namespace App\Services;

class NationalHolidayService
{
    /**
     * Palabras clave para identificar feriados rotativos por su título.
     */
    protected array $rotativeKeywords = [
        'carnaval',
        'jueves santo',
        'viernes santo',
    ];

    /**
     * Determina si una fecha (MM-DD) es un feriado fijo nacional.
     */
    public function isFixed(string $monthDay): bool
    {
        return isset(config('holidays.fixed', [])[$monthDay]);
    }

    /**
     * Determina si un título de evento corresponde a un feriado rotativo.
     */
    public function isRotativeByTitle(string $title): bool
    {
        $titleLower = mb_strtolower(trim($title), 'UTF-8');

        foreach ($this->rotativeKeywords as $keyword) {
            if (str_contains($titleLower, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determina si una fecha y título corresponden a un día no laborable
     * (feriado fijo o rotativo).
     */
    public function isNonWorkingDay(string $monthDay, string $title): bool
    {
        return $this->isFixed($monthDay) || $this->isRotativeByTitle($title);
    }
}
