<?php

namespace App\Services;

use Carbon\Carbon;

class HolidayService
{
    protected GoogleCalendarService $googleCalendarService;

    /**
     * Fuente única de feriados fijos: config/holidays.php
     */
    protected function fixedHolidays(): array
    {
        return config('holidays.fixed', []);
    }

    public function __construct(GoogleCalendarService $googleCalendarService)
    {
        $this->googleCalendarService = $googleCalendarService;
    }

    /**
     * Obtiene todos los días feriados (fijos + rotativos) en el rango de fechas
     * Retorna: ['YYYY-MM-DD' => ['title' => 'Nombre', 'nonWorking' => true]]
     */
    public function getHolidaysInRange(Carbon $start, Carbon $end): array
    {
        $holidayMap = [];
        $years = array_unique([$start->year, $end->year]);

        foreach ($years as $year) {
            // Mapear Feriados Fijos
            foreach ($this->fixedHolidays() as $monthDay => $title) {
                $dateKey = "{$year}-{$monthDay}";
                $holidayMap[$dateKey] = [
                    'title' => $title,
                    'nonWorking' => true
                ];
            }

            // Mapear Feriados Rotativos desde Google Calendar
            $googleEvents = $this->googleCalendarService->fetchHolidays($year);
            if (!empty($googleEvents)) {
                foreach ($googleEvents as $event) {
                    $titleLower = mb_strtolower($event['title'] ?? '', 'UTF-8');

                    if (str_contains($titleLower, 'carnaval') || 
                        str_contains($titleLower, 'jueves santo') || 
                        str_contains($titleLower, 'viernes santo')) {
                        
                        $dateKey = substr($event['start'], 0, 10); // Extrae YYYY-MM-DD
                        $holidayMap[$dateKey] = [
                            'title' => $event['title'] ?? 'Feriado Rotativo',
                            'nonWorking' => true
                        ];
                    }
                }
            }
        }

        return $holidayMap;
    }
}