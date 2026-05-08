<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GoogleCalendarService
{
    protected string $apiKey;
    protected string $calendarId;

    public function __construct()
    {
        $this->apiKey = config('services.google.calendar_api_key');

        $this->calendarId = 'es.ve#holiday@group.v.calendar.google.com';
    }

    public function fetchHolidays(int $year)
    {
        return Cache::remember(
            "google_holidays_{$year}",
            now()->addYears(1),
            function () use ($year) {

                $timeMin = "{$year}-01-01T00:00:00Z";
                $timeMax = "{$year}-12-31T23:59:59Z";

                $url = "https://www.googleapis.com/calendar/v3/calendars/" .
                    urlencode($this->calendarId) .
                    "/events";

                $response = Http::get($url, [
                    'key' => $this->apiKey,
                    'timeMin' => $timeMin,
                    'timeMax' => $timeMax,
                    'singleEvents' => 'true',
                    'orderBy' => 'startTime',
                ]);

                if (!$response->successful()) {

                    logger()->error('Google Calendar Error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    return [];
                }

                $data = $response->json();

                return collect($data['items'] ?? [])
                    ->map(fn($event) => $this->mapGoogleEvent($event))
                    ->values();
            }
        );
    }

    protected function mapGoogleEvent(array $event): array
    {
        $dateStr =
            $event['start']['date']
            ?? $event['start']['dateTime']
            ?? null;

        $monthDay = substr($dateStr, 5, 5);

        // eventos fijos venezolanos
        $fixedEventsList = [
            '01-01', '05-01', '06-24', '07-05', '07-24', '10-12', '12-24', '12-25', '12-31'
        ];

        $isFixed = in_array($monthDay, $fixedEventsList);

        return [

            'id' => $event['id'] ?? null,

            'title' => $event['summary'] ?? 'Sin título',

            'start' => isset($event['start']['date'])
                ? $event['start']['date'] . 'T00:00:00'
                : ($event['start']['dateTime'] ?? null),

            'allDay' => isset($event['start']['date']),

            'display' => 'block',

            'extendedProps' => [

                'category' => [
                    'key' => 'google-calendar',
                    'label' => 'Calendario Google',
                    'color' => 'g-calendar-ve-holidays',
                ],

                'description' =>
                    $event['description']
                    ?? 'Feriado oficial de Venezuela',

                'externalDate' => true,

                'repeatEvent' => true,

                'repeatInterval' => $isFixed
                    ? 'YEARLY'
                    : 'ROTATIVE',

                'isFixed' => $isFixed,

                'createdBy' => 'Calendario Google',
            ],
        ];
    }
}