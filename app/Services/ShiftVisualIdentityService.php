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

            $shift->letterShift = $identity['code'];
            $shift->color = $identity['color'];
        });

        $freeDayShift = new Shift();

        $freeDayShift->id = 0;
        $freeDayShift->description = 'Libre';
        $freeDayShift->available = 'yes';
        $freeDayShift->letterShift = 'L';
        $freeDayShift->color = '#535759';

        return $shifts->prepend($freeDayShift);
    }
}