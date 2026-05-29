<?php

namespace App\Services;

use App\Models\Shift;
use Illuminate\Support\Collection;

class ShiftVisualIdentityService
{
    private array $palette = [
        0 => ['code' => 'A', 'color' => '#FBBD08'],
        1 => ['code' => 'B', 'color' => '#ef4444'],
        2 => ['code' => 'C', 'color' => '#10b981'],
    ];

    public function apply(Collection $shifts): Collection
    {
        $shifts->each(function ($shift, $index) {

            $identity = $this->palette[$index] ?? ['code' => 'X', 'color' => '#6b7280'];

            $shift->letter_shift = $identity['code'];
            $shift->color = $identity['color'];
        });

        $freeDayShift = new Shift();

        $freeDayShift->id = 0;
        $freeDayShift->description = 'Libre';
        $freeDayShift->available = 'yes';
        $freeDayShift->code = 'L';
        $freeDayShift->letter_shift = 'L';
        $freeDayShift->color = '#535759';

        $absenceShift = new Shift();
        $absenceShift->id = -1; // ID negativo único para que no choque con la BD
        $absenceShift->description = 'Vacaciones';
        $absenceShift->available = 'yes';
        $absenceShift->code = 'VAC'; // Más adelante serán varios tipos (ausencias)
        $absenceShift->letter_shift = 'VAC';
        $absenceShift->color = '#a6a7a9';

        $shifts->prepend($freeDayShift);
        $shifts->prepend($absenceShift);

        return $shifts;
    }
}