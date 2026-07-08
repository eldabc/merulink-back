<?php

namespace App\Services\Scraping;

use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Scraper IVSS usando Chrome headless.
 *
 * El servidor GlassFish bloquea peticiones HTTP automatizadas (Guzzle/cURL).
 * Usamos Chrome headless con un HTML temporal que auto-envía el formulario,
 * simulando exactamente lo que hace un navegador real.
 */
class IvssScraper extends BaseScraper
{
    private const RESULTS_URL = 'http://www.ivss.gob.ve:28083/CuentaIndividualIntranet/CtaIndividual_PortalCTRL';
    private const CHROME_PATH  = 'C:/Program Files/Google/Chrome/Application/chrome.exe';

    public function scrape(string $ci, string $birthdate): array
    {
        $ciNormalized = $this->normalizeCi($ci);
        $birthFormatted = $this->formatBirthdate($birthdate);
        $parts = explode('/', $birthFormatted);

        Log::info("IVSS Chrome Scraper: CI={$ciNormalized}, FN={$birthFormatted}");

        $html = $this->submitViaChrome($ciNormalized, $parts[0], (int)$parts[1], $parts[2]);

        return $this->parseResultsPage($html);
    }

    /**
     * Crea un HTML temporal con un formulario que se auto-envía al IVSS,
     * lo ejecuta en Chrome headless y retorna el HTML resultante.
     */
    private function submitViaChrome(string $ci, string $day, int $month, string $year): string
    {
        $tmpFile = storage_path('app/tmp_ivss_' . uniqid() . '.html');

        $formHtml = '<!DOCTYPE html><html><body>
<form id="f" action="' . self::RESULTS_URL . '" method="post" target="_self" accept-charset="ISO-8859-1">
<input name="nationalidad_aseg" value="V">
<input name="cedula_aseg" value="' . $ci . '">
<input name="d" value="' . $day . '">
<input name="m" value="' . $month . '">
<input name="y" value="' . $year . '">
<input name="boton" value="Consultar">
</form>
<script>document.getElementById("f").submit();</script>
</body></html>';

        file_put_contents($tmpFile, $formHtml);

        $cmd = '"' . self::CHROME_PATH . '"'
            . ' --headless --disable-gpu --no-sandbox'
            . ' --virtual-time-budget=15000'
            . ' --dump-dom'
            . ' "file:///' . str_replace('\\', '/', $tmpFile) . '"'
            . ' 2>nul';

        Log::info("IVSS: Ejecutando Chrome headless...");

        $output = [];
        $exitCode = 0;
        exec($cmd, $output, $exitCode);

        $result = implode("\n", $output);

        // Limpiar temp
        @unlink($tmpFile);

        if (empty(trim($result))) {
            throw new \RuntimeException("Chrome no devolvió resultados (exit: {$exitCode}).");
        }

        Log::info("IVSS: HTML recibido, " . strlen($result) . " bytes. ¿BELLO? " . (strpos($result, 'BELLO') !== false ? 'SÍ' : 'NO'));

        if (strpos($result, 'BELLO') === false && strpos($result, 'no esta registrada') !== false) {
            throw new \RuntimeException("El IVSS indica que la cédula no está registrada como asegurado.");
        }

        return $result;
    }

    // =========================================================================
    // PARSER
    // =========================================================================

    private function parseResultsPage(string $html): array
    {
        $result = [
            'ci'               => null,
            'first_name'       => null,
            'second_name'      => null,
            'last_name'        => null,
            'second_last_name' => null,
            'birthdate'        => null,
            'sex'              => null,
            'company_name'     => null,
            'company_code'     => null,
            'retire_date'      => null,
            'source'           => 'ivss',
        ];

        // Solo parsear si el HTML contiene los datos
        if (strpos($html, 'BELLO') === false && strpos($html, 'Nombre y Apellido') === false) {
            return $result;
        }

        $crawler = new Crawler($html);

        // Buscar <tr class="datos">
        try {
            $crawler->filter('tr.datos')->each(function (Crawler $tr) use (&$result) {
                $cells = $tr->filter('td');
                if ($cells->count() >= 2) {
                    $label = trim(preg_replace('/\s+/', ' ', $cells->eq(0)->text()));
                    $value = trim(preg_replace('/\s+/', ' ', $cells->eq(1)->text()));
                    $this->matchField($label, $value, $result);
                }
            });
        } catch (\Exception $e) {
            Log::warning("IVSS parser: " . $e->getMessage());
        }

        return $result;
    }

    private function matchField(string $label, string $value, array &$result): void
    {
        $label = strtolower(trim($label, ":\t\n\r\0\x0B "));

        if (str_contains($label, 'cédula') || $label === 'cedula de identidad') {
            $result['ci'] = $value;
            return;
        }
        if (str_contains($label, 'nombre') && str_contains($label, 'apellido')) {
            $this->parseFullName($value, $result);
            return;
        }
        if (str_contains($label, 'sexo')) {
            $result['sex'] = strtoupper(substr(trim($value), 0, 1)) === 'F' ? 'FEMENINO' : (strtoupper(substr(trim($value), 0, 1)) === 'M' ? 'MASCULINO' : strtoupper(trim($value)));
            return;
        }
        if (str_contains($label, 'fecha') && str_contains($label, 'nacimiento')) {
            $result['birthdate'] = $value;
            return;
        }
        if (str_contains($label, 'patronal')) {
            $result['company_code'] = $value;
            return;
        }
        if (str_contains($label, 'empresa') && str_contains($label, 'nombre')) {
            $result['company_name'] = $value;
            return;
        }
        if (str_contains($label, 'egreso')) {
            $result['retire_date'] = $value;
            return;
        }
    }

    private function parseFullName(string $fullName, array &$result): void
    {
        $parts = preg_split('/\s+/', trim($fullName));
        $count = count($parts);

        if ($count >= 4) {
            $result['last_name']        = $parts[0];
            $result['second_last_name'] = $parts[1];
            $result['first_name']       = $parts[$count - 2];
            $result['second_name']      = $parts[$count - 1];
        } elseif ($count === 3) {
            $result['first_name'] = $parts[0];
            $result['last_name']  = $parts[1];
            $result['second_last_name'] = $parts[2];
        } elseif ($count === 2) {
            $result['first_name'] = $parts[0];
            $result['last_name']  = $parts[1];
        } elseif ($count === 1) {
            $result['first_name'] = $parts[0];
        }
    }
}
