<?php

namespace App\Services;

use App\Models\Shift;
use Illuminate\Support\Collection;

class ShiftVisualIdentityService
{
    private array $palette = [
        0 => ['code' => 'A', 'color' => '#FBBD08'],
        1 => ['code' => 'B', 'color' => '#33a1c0'],
        2 => ['code' => 'C', 'color' => '#10b981'],
        3 => ['code' => 'D', 'color' => '#8b5cf6'],
    ];

    public function apply(Collection $shifts): Collection
    {
        // Ordena por check_in_time y resetea índices con ->values()
        $orderedShifts = $shifts->sortBy('check_in_time')->values();

        $orderedShifts->each(function ($shift, $index) {

            $identity = $this->palette[$index] ?? ['code' => 'X', 'color' => '#6b7280'];

            $shift->letter_shift = $identity['code'];
            $shift->color = $identity['color'];
        });

        return $orderedShifts;
    }
}