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
    private const MAX_RETRIES = 2;

    /**
     * Cliente HTTP específico para IVSS con opciones para servidores inestables.
     */
    private function makeIvssClient(): \GuzzleHttp\Client
    {
        return new \GuzzleHttp\Client([
            'timeout'         => 35,
            'connect_timeout' => 15,
            'allow_redirects' => true,
            'cookies'         => $this->cookieJar,
            'verify'          => false,
            'version'         => '1.1',              // forzar HTTP/1.1 (servidor viejo)
            'headers'         => [
                'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
                'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'es-VE,es;q=0.9,en;q=0.8',
                'Connection'      => 'close',        // no reutilizar conexión
            ],
            'curl' => [
                CURLOPT_FRESH_CONNECT  => true,       // conexión nueva cada request
                CURLOPT_FORBID_REUSE   => true,       // no reutilizar
            ],
        ]);
    }

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

        $lastException = null;

        for ($attempt = 0; $attempt <= self::MAX_RETRIES; $attempt++) {
            try {
                $client = $this->makeIvssClient();

                $response = $client->post(self::RESULTS_URL, [
                    'form_params' => $formData,
                ]);

                $html = (string) $response->getBody();

                Log::info("IVSS: HTTP {$response->getStatusCode()}, " . strlen($html) . " bytes.");

                return $this->parse($html);
            } catch (\GuzzleHttp\Exception\ConnectException $e) {
                $lastException = $e;
                Log::warning("IVSS: Intento {$attempt} — Error de conexión: " . $e->getMessage());
                if ($attempt < self::MAX_RETRIES) {
                    sleep(2); // esperar antes de reintentar
                }
            } catch (\Exception $e) {
                $lastException = $e;
                Log::warning("IVSS: Intento {$attempt} — Error: " . $e->getMessage());
                if ($attempt < self::MAX_RETRIES) {
                    sleep(2);
                }
            }
        }

        Log::warning("IVSS: Agotados " . (self::MAX_RETRIES + 1) . " intentos.");
        throw new \RuntimeException("No se pudo conectar con el IVSS: " . $lastException->getMessage());
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
        if (str_contains($label, 'fecha') && str_contains($label, 'nacimiento')) { $r['birthdate'] = $value; return; }
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
