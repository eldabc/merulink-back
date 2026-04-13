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
    public function calculateBirthdayEvents($history)
    {
        $today = \Carbon\Carbon::today()->startOfDay();
        $limitDate = $today->copy()->addMonths(2)->startOfDay();

        $employees = Employee::with('position.department')
            ->whereNotNull('birthdate')
            ->where('status', true)
            ->get()
            ->map(function ($employee) use ($today) {
                
                $birthdate = \Carbon\Carbon::parse($employee->birthdate);
                $nextBirthday = $birthdate->copy()->year($today->year)->startOfDay();

            if ($nextBirthday->lt($today)) {
                $nextBirthday->addYear();
            }

            $employee->next_birthday = $nextBirthday;

            return $employee;
        })
        ->filter(function ($employee) use ($today, $limitDate) {
            return $employee->next_birthday->between($today, $limitDate); // SOLO dentro del rango de 2 meses
        })
        ->sortBy('next_birthday')
        ->values();

        $eventCategory = EventCategory::where('key', 'meru-birthdays')->first();

        return  $employees->map(function ($employee) use ($today, $eventCategory) {
            return (new BirthdayEventResource($employee, $today, $eventCategory))->resolve();
        });

    }
}