<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EventCategory;
use Illuminate\Support\Facades\DB;
use App\Enums\LockerStatus;
use App\Http\Resources\BirthdayEventResource;
use Carbon\Carbon;

class BirthdayEventService {

    /**
     * Lógica centralizada para calcular eventos de cumpleaños.
    */
public function calculateBirthdayEvents($history = false, $year = null, $isAll = false)
{
    $currentYear = \Carbon\Carbon::today()->year;
    $selectedYear = $year ?: $currentYear;
    $today = \Carbon\Carbon::today()->year($selectedYear)->startOfDay();
    $eventCategory = EventCategory::where('key', 'meru-birthdays')->first();

    // Define los límites "MMDD" para base de datos
    if ($isAll || ($selectedYear !== $currentYear)) {
        $startLimit = "0101";
        $endLimit = "1231";
    } elseif ($history) {
        // Desde el 1 de enero hasta ayer
        $startLimit = "0101";
        $endLimit = $today->copy()->subDay()->format('md');
    } else {
        // Desde hoy hasta el 31 de diciembre
        $startLimit = $today->format('md');
        $endLimit = "1231";
    }

    $employees = Employee::with('position.department')
        ->whereNotNull('birthdate')
        ->where('status', true)
        ->whereRaw("to_char(birthdate, 'MMDD') BETWEEN ? AND ?", [$startLimit, $endLimit])
        ->get()
        ->map(function ($employee) use ($today) {
            $birthdate = \Carbon\Carbon::parse($employee->birthdate);
            $employee->next_birthday = $birthdate->copy()->year($today->year)->startOfDay();
            // Calcular la edad que cumple en el año seleccionado
            $employee->age_in_year = $today->year - $birthdate->year;
            return $employee;
        })
        ->sortBy('next_birthday', SORT_REGULAR, $history)
        ->values();

    return $employees->map(function ($employee) use ($today, $eventCategory) {
        return (new BirthdayEventResource($employee, $today, $eventCategory))->resolve();
    });
}
}