<?php

namespace App\Services\Scraping;

use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Scraper SENIAT (BuscaRif).
 *
 * URL: http://contribuyente.seniat.gob.ve/BuscaRif/BuscaRif.jsp
 * Método: POST
 * Campos: p_cedula, codigo (captcha)
 *
 * El sitio tiene CAPTCHA (Captcha.jpg). No se puede resolver automáticamente.
 * Flujo: obtener captcha → usuario lo resuelve → enviar p_cedula + codigo.
 */
class SeniatScraper extends BaseScraper
{
    private const BASE_URL = 'http://contribuyente.seniat.gob.ve/BuscaRif/BuscaRif.jsp';

    /**
     * Obtiene la imagen del captcha y la devuelve en base64,
     * junto con las cookies de sesión necesarias para el POST posterior.
     *
     * @return array{captcha: string, cookies: array}
     */
    public function getCaptcha(): array
    {
        // GET a la página para obtener JSESSIONID y la URL del captcha
        $response = $this->httpClient->get(self::BASE_URL);
        $html = (string) $response->getBody();

        // Descargar la imagen del captcha
        $captchaResponse = $this->httpClient->get('http://contribuyente.seniat.gob.ve/BuscaRif/Captcha.jpg');
        $captchaBytes = (string) $captchaResponse->getBody();

        // Extraer cookies para mantener la sesión
        $cookies = [];
        foreach ($this->cookieJar->toArray() as $c) {
            if (isset($c['Name'], $c['Value'])) {
                $cookies[$c['Name']] = $c['Value'];
            }
        }

        return [
            'captcha' => 'data:image/jpeg;base64,' . base64_encode($captchaBytes),
            'cookies' => $cookies,
        ];
    }

    /**
     * Envía la consulta al SENIAT con cédula + código captcha.
     *
     * @param string $ci     Cédula (ej: "21380780")
     * @param string $codigo Código captcha ingresado por el usuario
     * @return array Datos extraídos
     */
    public function scrape(string $ci, string $codigo = null): array
    {
        $ciNorm = $this->normalizeCi($ci);

        if (empty($codigo)) {
            throw new \RuntimeException("Se requiere el código {$codigo} captcha para consultar el SENIAT.");
        }

        Log::info("SENIAT: Consultando CI={$ciNorm}, codigo={$codigo}");

        $response = $this->httpClient->post(self::BASE_URL, [
            'form_params' => [
                'p_cedula' => $ciNorm,
                'codigo'   => $codigo,
            ],
        ]);

        $html = (string) $response->getBody();

        return $this->parse($html);
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
        ];

        $crawler = new Crawler($html);

        try {
            // El nombre viene en: <b><font>V123456780&nbsp;NOMBRE COMPLETO USUARIO</font></b>
            $nameNode = $crawler->filter('table[align="center"] b font');
            if ($nameNode->count() > 0) {
                $fullText = trim($nameNode->first()->text());

                // Formato: "V123456780 NOMBRE COMPLETO USUARIO"
                // Quitar el RIF del inicio (V/E/J + números)
                $fullText = preg_replace('/^[VEJ]\d{7,9}\s*/i', '', $fullText);
                $this->splitName($fullText, $result);
            }

            // Intentar obtener RIF/CI del texto
            if (preg_match('/([VEJ]\d{7,9})/i', $html, $m)) {
                $result['ci'] = $m[1];
            }
        } catch (\Exception $e) {
            Log::warning("SENIAT parser: " . $e->getMessage());
        }

        // Verificar si hay mensaje de error
        if (empty($result['first_name']) && empty($result['last_name'])) {
            if (str_contains($html, 'no esta registrado') || str_contains($html, 'No Existe')) {
                Log::info("SENIAT: Cédula no encontrada.");
            } elseif (str_contains($html, 'codigo') && str_contains($html, 'incorrecto')) {
                Log::info("SENIAT: Código captcha incorrecto.");
                throw new \RuntimeException("El código captcha ingresado es incorrecto.");
            }
        }

        Log::info("SENIAT: Parseado — nombre=" . ($result['first_name'] ?? 'null'));

        return $result;
    }

    /**
     * Parsea nombre en formato SENIAT: "V123456780 NOMBRE COMPLETO USUARIO"
     * (nombres primero, apellidos después — inverso al IVSS).
     */
    private function splitName(string $name, array &$r): void
    {
        $parts = preg_split('/\s+/', trim($name));
        $c = count($parts);

        if ($c >= 4) {
            // SENIAT: V123456780 NOMBRE COMPLETO USUARIO → nombres luego apellidos
            $r['first_name']       = $parts[0];
            $r['second_name']      = $parts[1];
            $r['last_name']        = $parts[2];
            $r['second_last_name'] = $parts[3];
        } elseif ($c === 3) {
            $r['first_name'] = $parts[0];
            $r['last_name']  = $parts[1];
            $r['second_last_name'] = $parts[2];
        } elseif ($c === 2) {
            $r['first_name'] = $parts[0];
            $r['last_name']  = $parts[1];
        } elseif ($c === 1) {
            $r['first_name'] = $parts[0];
        }
    }
}
