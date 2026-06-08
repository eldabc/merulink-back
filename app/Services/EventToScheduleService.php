<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Collection;

class EventToScheduleService
{
    /**
     * Obtiene y mapea los eventos con resaltado activo dentro de un rango quincenal/mensual.
     *
     * @param string $start Fecha de inicio (YYYY-MM-DD)
     * @param string $end Fecha de fin (YYYY-MM-DD)
     * @return Collection
     */
    public function getHighlightedEventsForPeriod(string $start, string $end): Collection
    {
        return Event::query()
            ->where(function ($q) use ($start, $end) {
                // Compara limpiamente Año-Mes-Día ignorando horas
                $q->whereDate('start', '>=', $start)->whereDate('start', '<=', $end)
                  ->orWhereDate('end', '>=', $start)->whereDate('end', '<=', $end)
                  ->orWhere(function ($deep) use ($start, $end) {
                      $deep->whereDate('start', '<=', $start)
                           ->whereDate('end', '>=', $end);
                  });
            })
            ->where('extended_props->coloring_day', true)
            ->get(['title', 'start', 'end', 'extended_props'])
            ->map(function ($event) {
                return [
                    'title'        => $event->title,
                    'start'        => $event->start,
                    'end'          => $event->end,
                    'coloring_day' => $event->extended_props['coloring_day'] ?? true,
                ];
            });
    }
}