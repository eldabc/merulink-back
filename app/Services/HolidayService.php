<?php

namespace App\Services;

use Carbon\Carbon;

class HolidayService
{
    protected GoogleCalendarService $googleCalendarService;

    // Feriados fijos oficiales de Venezuela
    protected array $fixedHolidays = [
        '01-01', '05-01', '06-24', '07-05', '07-24', '10-12', '12-24', '12-25', '12-31'
    ];

    public function __construct(GoogleCalendarService $googleCalendarService)
    {
        $this->googleCalendarService = $googleCalendarService;
    }

    /**
     * Obtiene todos los días feriados (fijos + rotativos) en un rango de fechas.
     */
    public function getHolidaysInRange(Carbon $start, Carbon $end): array
    {
        $holidays = [];
        $years = array_unique([$start->year, $end->year]);

        foreach ($years as $year) {
            // Añadir feriados fijos
            foreach ($this->fixedHolidays as $fixed) {
                $holidays[] = "{$year}-{$fixed}";
            }

            // Extraer y filtrar eventos rotativos desde tu GoogleCalendarService
            $googleEvents = $this->googleCalendarService->fetchHolidays($year);
            if (!empty($googleEvents)) {
                foreach ($googleEvents as $event) {
                    $titleLower = mb_strtolower($event['title'] ?? '', 'UTF-8');

                    if (str_contains($titleLower, 'carnaval') || 
                        str_contains($titleLower, 'jueves santo') || 
                        str_contains($titleLower, 'viernes santo')) {
                        
                        // Extraer solo porción YYYY-MM-DD
                        $holidays[] = substr($event['start'], 0, 10);
                    }
                }
            }
        }

        return array_unique($holidays);
    }
}