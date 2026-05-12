<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EventCategory;
use Illuminate\Support\Facades\DB;
use App\Enums\LockerStatus;
use App\Http\Resources\BirthdayEventResource;
use Carbon\Carbon;

class BirthdayEventService
{
    /**
     * Calcular cumpleaños según rango de fechas.
     */
    public function calculateBirthdayEvents(
        $startDate,
        $endDate,
        int $year,
        bool $history = false
    ) {

        $today = \Carbon\Carbon::today()
            ->year($year)
            ->startOfDay();

        $eventCategory = EventCategory::where(
            'key',
            'meru-birthdays'
        )->first();

        $employees = Employee::with('position.department')
            ->whereNotNull('birthdate')
            ->where('status', true)
            ->get()

            ->map(function ($employee) use ($year) {

                $birthdate = \Carbon\Carbon::parse(
                    $employee->birthdate
                );

                $birthdayThisYear = $birthdate
                    ->copy()
                    ->year($year)
                    ->startOfDay();

                $employee->next_birthday = $birthdayThisYear;

                $employee->age_in_year =
                    $year - $birthdate->year;

                return $employee;
            })

            ->filter(function ($employee) use (
                $startDate,
                $endDate
            ) {

                $birthday = $employee->next_birthday;

                // rango completo
                if ($startDate && $endDate) {

                    return $birthday->between(
                        $startDate,
                        $endDate
                    );
                }

                // historial
                if (!$startDate && $endDate) {

                    return $birthday->lt($endDate);
                }

                // futuros
                if ($startDate && !$endDate) {

                    return $birthday->gte($startDate);
                }

                return true;
            })

            ->sortBy(
                'next_birthday',
                SORT_REGULAR,
                $history
            )

            ->values();

        return $employees->map(function (
            $employee
        ) use (
            $today,
            $eventCategory
        ) {

            return (
                new BirthdayEventResource(
                    $employee,
                    $today,
                    $eventCategory
                )
            )->resolve();
        });
    }
}