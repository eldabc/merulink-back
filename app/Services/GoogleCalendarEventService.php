<?php 

namespace App\Services;

use Carbon\Carbon;
use App\Models\Event;

use App\Services\GoogleCalendarService;
use Illuminate\Support\Collection;

class GoogleCalendarEventService
{
    public function __construct(
        protected GoogleCalendarService $googleCalendarService
    ) {}

    public function getFilteredEvents(
        int $year,
        ?Carbon $startDate,
        ?Carbon $endDate,
        bool $applyHistory,
        Carbon $today
    ): Collection {

        // IDs registrados en BD
        $internalStoreGoogleEvents = Event::query()
            ->whereHas('eventCategory', function($q) {
                $q->where('key', 'google-calendar');
            })
            ->whereYear('start', $year)
            ->pluck('external_id')
            ->filter()
            ->flip();

        // eventos google
        $googleEvents = collect(
            $this->googleCalendarService->fetchHolidays($year)
        );

        return $googleEvents

            // quitar duplicados
            ->filter(function ($event) use ($internalStoreGoogleEvents) {

                return !isset(
                    $internalStoreGoogleEvents[$event['id']]
                );
            })

            // filtro fecha
            ->filter(function ($event) use (
                $startDate,
                $endDate,
                $applyHistory,
                $today
            ) {

                $eventDate = Carbon::parse($event['start']);

                if ($startDate && $endDate) {
                    return $eventDate->between(
                        $startDate,
                        $endDate
                    );
                }

                if ($applyHistory) {
                    return $eventDate->lt($today);
                }

                return $eventDate->gte($today);
            })

            // ordenar
            ->sortBy(function ($event) {

                return Carbon::parse(
                    $event['start']
                )->timestamp;

            }, SORT_REGULAR, $applyHistory)

            ->values();
    }
}