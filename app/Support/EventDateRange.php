<?php

namespace App\Support;

use Carbon\Carbon;

class EventDateRange
{
    public static function resolve(
        int $year,
        ?int $month,
        bool $getAllYear,
        bool $applyHistory,
        Carbon $today
    ): array {

        // rango mensual
        if ($month) {

            $startDate = now()
                ->setYear($year)
                ->setMonth($month)
                ->startOfMonth();

            $endDate = now()
                ->setYear($year)
                ->setMonth($month)
                ->endOfMonth();

        }

        // rango anual
        elseif ($getAllYear) {

            $startDate = now()
                ->setYear($year)
                ->startOfYear();

            $endDate = now()
                ->setYear($year)
                ->endOfYear();

        }

        // historial
        elseif ($applyHistory) {

            $startDate = null;
            $endDate = $today;

        }

        // futuros
        else {

            $startDate = $today;
            $endDate = null;
        }

        return [$startDate, $endDate];
    }
}