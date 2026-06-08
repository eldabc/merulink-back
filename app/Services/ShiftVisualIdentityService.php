<?php

namespace App\Services;

use App\Models\Shift;
use Illuminate\Support\Collection;
// use App\Enums\SystemShift;

class ShiftVisualIdentityService
{
    private array $palette = [
        0 => ['code' => 'A', 'color' => '#FBBD08'],
        1 => ['code' => 'B', 'color' => '#447cef'],
        2 => ['code' => 'C', 'color' => '#10b981'],
        3 => ['code' => 'D', 'color' => '#8b5cf6'],
    ];

    public function apply(Collection $shifts): Collection
    {
        $shifts->each(function ($shift, $index) {

            $identity = $this->palette[$index] ?? ['code' => 'X', 'color' => '#6b7280'];

            $shift->letter_shift = $identity['code'];
            $shift->color = $identity['color'];
        });

        // $systemFreeShift = SystemShift::FREE->getData();
        // $systemVacationShift = SystemShift::VACATIONS->getData();

        // $freeDayShift = new Shift();

        // $freeDayShift->id = $systemFreeShift['id'];        
        // $freeDayShift->code = $systemFreeShift['code'];
        // $freeDayShift->letter_shift = $systemFreeShift['letterShift'];
        // $freeDayShift->color = $systemFreeShift['color'];
        // $freeDayShift->description = $systemFreeShift['description'];

        // $absenceShift = new Shift();

        // $absenceShift->id = $systemVacationShift['id'];
        // $absenceShift->code = $systemVacationShift['code']; // Más adelante serán varios tipos (ausencias)
        // $absenceShift->letter_shift = $systemVacationShift['letterShift'];
        // $absenceShift->color = $systemVacationShift['color'];
        // $absenceShift->description = $systemVacationShift['description'];

        // $shifts->prepend($freeDayShift);
        // $shifts->prepend($absenceShift);

        return $shifts;
    }
}