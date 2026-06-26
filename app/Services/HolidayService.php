<?php

namespace App\Services;

use Carbon\Carbon;

class HolidayService
{
    protected GoogleCalendarService $googleCalendarService;

    // Feriados fijos oficiales de VE
    protected array $fixedHolidays = [
        '01-01' => 'Año Nuevo',
        '05-01' => 'Día del Trabajador',
        '06-24' => 'Batalla de Carabobo',
        '07-05' => 'Día de la Independencia',
        '07-24' => 'Natalicio de Simón Bolívar',
        '10-12' => 'Día de la Resistencia Indígena',
        '12-24' => 'Víspera de Navidad',
        '12-25' => 'Navidad',
        '12-31' => 'Fin de Año',
    ];

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
            foreach ($this->fixedHolidays as $monthDay => $title) {
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