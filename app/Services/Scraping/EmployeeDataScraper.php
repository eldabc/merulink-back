<?php

namespace App\Services\Scraping;

use Illuminate\Support\Facades\Log;

/**
 * Orquestador de scraping por source.
 *
 * El frontend decide qué servicio usar:
 *   source=ivss   → IvssScraper  (requiere ci + birthdate)
 *   source=seniat → SeniatScraper (requiere ci + seniat_code)
 */
class EmployeeDataScraper
{
    private const SCRAPERS = [
        'ivss'   => IvssScraper::class,
        'seniat' => SeniatScraper::class,
    ];

    public function fetchBySource(string $source, string $ci, string $birthdate, ?string $seniatCode = null): array
    {
        if (!isset(self::SCRAPERS[$source])) {
            return $this->fail($ci, "Fuente no válida: {$source}");
        }

        $class = self::SCRAPERS[$source];
        Log::info("EmployeeDataScraper: Ejecutando {$source}...");

        try {
            /** @var BaseScraper $scraper */
            $scraper = app($class);

            // TODO: quitar tras testear SENIAT
            if ($source === 'ivss') {
                Log::info("EmployeeDataScraper: Simulando fallo de IVSS.");
                throw new \RuntimeException("IVSS simulado como fallido.");
            }

            $data = match ($source) {
                'ivss'   => $scraper->scrape($ci, $birthdate),
                'seniat' => $scraper->scrape($ci, $seniatCode),
            };

            if (!empty($data['first_name']) && !empty($data['last_name'])) {
                return [
                    'success' => true,
                    'data'    => $this->formatResponse($data, $ci),
                    'source'  => $source,
                    'error'   => null,
                ];
            }

            Log::info("EmployeeDataScraper: {$source} respondió pero sin datos de nombre.");
        } catch (\RuntimeException $e) {
            Log::info("EmployeeDataScraper: {$source} falló — " . $e->getMessage());
            return $this->fail($ci, $e->getMessage());
        }

        return $this->fail($ci, "No se encontraron datos del empleado en {$source}.");
    }

    private function fail(string $ci, string $msg): array
    {
        return [
            'success' => false,
            'data'    => $this->emptyResponse($ci),
            'source'  => 'manual',
            'error'   => $msg,
        ];
    }

    /**
     * Formatea la respuesta con los datos obtenidos, asegurando que la CI esté presente.
     */
    private function formatResponse(array $data, string $ci): array
    {
        return [
            'ci'               => $data['ci'] ?? $ci,
            'first_name'       => $data['first_name'] ?? null,
            'second_name'      => $data['second_name'] ?? null,
            'last_name'        => $data['last_name'] ?? null,
            'second_last_name' => $data['second_last_name'] ?? null,
            'birthdate'        => $data['birthdate'] ?? null,
            'sex'              => $data['sex'] ?? '',
            'nationality'      => $data['nationality'] ?? '',
            'source'           => $data['source'] ?? '',
        ];
    }

    /**
     * Respuesta vacía para fallback manual.
     */
    private function emptyResponse(string $ci): array
    {
        return [
            'ci'               => $ci,
            'first_name'       => null,
            'second_name'      => null,
            'last_name'        => null,
            'second_last_name' => null,
            'birthdate'        => null,
            'sex'              => null,
            'nationality'      => 'V',
            'source'           => 'manual',
        ];
    }

    /**
     * Normaliza una fecha a formato Y-m-d (el que usa la BD).
     */
    private function normalizeDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        // Si ya está en Y-m-d
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        // Si está en d/m/Y (formato IVSS)
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $m)) {
            return "{$m[3]}-{$m[2]}-{$m[1]}";
        }

        try {
            return (new \DateTime($date))->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
