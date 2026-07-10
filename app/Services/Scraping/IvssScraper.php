<?php

namespace App\Services\Scraping;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Scraper IVSS usando Guzzle HTTP.
 *
 * Campos: nacionalidad_aseg, cedula_aseg, d, m (sin zero), y, boton
 *
 * URL: http://www.ivss.gob.ve:28083/CuentaIndividualIntranet/CtaIndividual_PortalCTRL
 */
class IvssScraper extends BaseScraper
{
    private const RESULTS_URL = 'http://www.ivss.gob.ve:28083/CuentaIndividualIntranet/CtaIndividual_PortalCTRL';

    public function scrape(string $ci, string $birthdate): array
    {
        $ciNorm = $this->normalizeCi($ci);
        $birth   = $this->formatBirthdate($birthdate);
        $parts   = explode('/', $birth);

        Log::info("IVSS: CI={$ciNorm}, FN={$birth}");

        $formData = [
            'nacionalidad_aseg' => 'V',
            'cedula_aseg'       => $ciNorm,
            'd'                 => (int) ($parts[0]),
            'm'                 => (int) ($parts[1]),
            'y'                 => $parts[2],
            'boton'             => 'Consultar',
        ];

        try {
            $response = $this->httpClient->post(self::RESULTS_URL, [
                'form_params' => $formData,
            ]);

            $html = (string) $response->getBody();

            Log::info("IVSS: HTTP {$response->getStatusCode()}, " . strlen($html) . " bytes.");

            return $this->parse($html);
        } catch (\Exception $e) {
            Log::warning("IVSS: Error HTTP — " . $e->getMessage());
            throw new \RuntimeException("No se pudo conectar con el IVSS: " . $e->getMessage());
        }
    }

    // ============================================================
    // PARSER
    // ============================================================
    private function parse(string $html): array
    {
        $result = [
            'ci'               => null,
            'first_name'       => null,
            'second_name'      => null,
            'last_name'        => null,
            'second_last_name' => null,
            'birthdate'        => null,
            'sex'              => null,
        ];

        // ¿Es la página de error?
        if (str_contains($html, 'no esta registrada')) {
            Log::info("IVSS: La cédula no está registrada como asegurado.". $html . " bytes.");
            return $result;
        }

        // ¿Tiene los datos?
        if (!str_contains($html, 'Datos del Asegurado')) {
            Log::warning("IVSS: HTML recibido no contiene 'Datos del Asegurado'. Primeros 300 chars: " . substr($html, 0, 300));
            return $result;
        }

        $crawler = new Crawler($html);

        try {
            $crawler->filter('tr.datos')->each(function (Crawler $tr) use (&$result) {
                $cells = $tr->filter('td');
                if ($cells->count() >= 2) {
                    $label = $this->clean($cells->eq(0)->text());
                    $value = $this->clean($cells->eq(1)->text());
                    $this->match($label, $value, $result);
                }
            });
        } catch (\Exception $e) {
            Log::warning("IVSS parser error: " . $e->getMessage());
        }

        Log::info("IVSS: Parseado — nombre=" . ($result['first_name'] ?? 'null') . ", ci=" . ($result['ci'] ?? 'null'));

        return $result;
    }

    private function match(string $label, string $value, array &$r): void
    {
        $label = strtolower(trim($label, ":\t\n\r\0\x0B "));

        if (str_contains($label, 'cédula'))          { $r['ci'] = $value; return; }
        if (str_contains($label, 'nombre') && str_contains($label, 'apellido')) { $this->splitName($value, $r); return; }
        if (str_contains($label, 'sexo'))            { $r['sex'] = strtoupper(trim($value)); return; }
        if (str_contains($label, 'fecha') && str_contains($label, 'nacimiento')) { $r['birthdate'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d'); return; }
    }

    private function splitName(string $name, array &$r): void
    {
        $parts = preg_split('/\s+/', trim($name));
        $c = count($parts);
        if ($c >= 4) {
            $r['last_name'] = $parts[0]; $r['second_last_name'] = $parts[1];
            $r['first_name'] = $parts[$c-2]; $r['second_name'] = $parts[$c-1];
        } elseif ($c === 3) {
            $r['first_name'] = $parts[0]; $r['last_name'] = $parts[1]; $r['second_last_name'] = $parts[2];
        } elseif ($c === 2) {
            $r['first_name'] = $parts[0]; $r['last_name'] = $parts[1];
        } elseif ($c === 1) {
            $r['first_name'] = $parts[0];
        }
    }

    private function clean(string $text): string
    {
        return trim(preg_replace('/\s+/', ' ', $text));
    }
}
