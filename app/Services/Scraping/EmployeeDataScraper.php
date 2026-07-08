<?php

namespace App\Services\Scraping;

use Illuminate\Support\Facades\Log;

/**
 * Orquestador de scraping de datos de empleados.
 *
 * Intenta obtener datos del empleado desde múltiples fuentes en orden:
 *   1. IVSS (http://www.ivss.gov.ve/)
 *   2. SENIAT (http://contribuyente.seniat.gob.ve/) — placeholder
 *   3. Fallback: retorna campos vacíos para llenado manual
 *
 * Cada scraper tiene 30s para responder.
 */
class EmployeeDataScraper
{
    /**
     * @param string $ci        Cédula de identidad
     * @param string $birthdate Fecha de nacimiento (acepta d/m/Y, Y-m-d, etc.)
     * @return array{success: bool, data: array, source: string, error: string|null}
     */
    public function fetch(string $ci, string $birthdate): array
    {
        $scrapers = [
            'ivss'   => IvssScraper::class,
            'seniat' => SeniatScraper::class,
        ];

        foreach ($scrapers as $name => $class) {
            Log::info("EmployeeDataScraper: Intentando con {$name}...");
            
            try {
                /** @var BaseScraper $scraper */
                $scraper = app($class);
                $data = $scraper->scrape($ci, $birthdate);

                if (!empty($data['first_name']) || !empty($data['last_name'])) {
                    return [
                        'success' => true,
                        'data'    => $this->formatResponse($data, $ci),
                        'source'  => $name,
                        'error'   => null,
                    ];
                }

                Log::info("EmployeeDataScraper: {$name} respondió pero sin datos de nombre.");

            } catch (\RuntimeException $e) {
                Log::info("EmployeeDataScraper: {$name} falló — " . $e->getMessage());
            }
        }

        // Si todos fallan, retornamos datos mínimos (solo CI) para que el usuario llene manualmente
        return [
            'success' => false,
            'data'    => $this->emptyResponse($ci),
            'source'  => 'manual',
            'error'   => 'No se pudo obtener información del empleado desde ninguna fuente. Complete los datos manualmente.',
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
            'birthdate'        => $this->normalizeDate($data['birthdate'] ?? null),
            'sex'              => $data['sex'] ?? null,
            'nationality'      => $data['nationality'] ?? 'V',
            'company_name'     => $data['company_name'] ?? null,
            'company_code'     => $data['company_code'] ?? null,
            'retire_date'      => $this->normalizeDate($data['retire_date'] ?? null),
            'source'           => $data['source'] ?? 'ivss',
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
            'company_name'     => null,
            'company_code'     => null,
            'retire_date'      => null,
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
