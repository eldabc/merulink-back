<?php

namespace App\Services\Scraping;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
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
        $response = $this->httpClient->get(self::BASE_URL);
        $html = (string) $response->getBody();

        // Descargar la imagen del captcha
        $captchaResponse = $this->httpClient->get('http://contribuyente.seniat.gob.ve/BuscaRif/Captcha.jpg');
        $captchaBytes = (string) $captchaResponse->getBody();

        // Guardar cookies de sesión para que scrape() las use
        Cache::put('seniat_jar', serialize($this->cookieJar), 120);

        return [
            'captcha' => 'data:image/jpeg;base64,' . base64_encode($captchaBytes),
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
            throw new \RuntimeException("Se requiere el código captcha para consultar el SENIAT.");
        }

        // Restaurar cookies de sesión del captcha (mismo JSESSIONID)
        $cachedJar = Cache::get('seniat_jar');
        if ($cachedJar) {
            $this->cookieJar = unserialize($cachedJar);
            $this->httpClient = new \GuzzleHttp\Client([
                'timeout'         => 30,
                'connect_timeout' => 10,
                'allow_redirects' => true,
                'cookies'         => $this->cookieJar,
                'verify'          => false,
                'headers'         => [
                    'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36',
                    'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'es-VE,es;q=0.9,en;q=0.8',
                ],
            ]);
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
            // El nombre y los errores comparten el mismo nodo:
            // Éxito:  <b><font>V123456789 NOMBRE COMPLETO</font></b>
            // Error:  <b><font>EL código no coincide con la imagen.</font></b>
            $nameNode = $crawler->filter('table[align="center"] b font');
            if ($nameNode->count() > 0) {
                $fullText = trim($nameNode->first()->text());
                Log::info("SENIAT: texto extraído — \"{$fullText}\"");

                // Detectar error de captcha en el texto del nodo
                $this->throwIfCaptchaError($fullText);

                // Quitar el RIF del inicio si existe (V/E/J + números)
                $fullText = preg_replace('/^[VEJ]\d{7,9}\s*/i', '', $fullText);
                $this->splitName($fullText, $result);
            }
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::warning("SENIAT parser: " . $e->getMessage());
        }

        Log::info("SENIAT: Parseado — nombre=" . ($result['first_name'] ?? 'null'));

        return $result;
    }

    /**
     * Revisa el texto del nodo de resultado en busca de errores de captcha.
     */
    private function throwIfCaptchaError(string $text): void
    {
        $patterns = ['no coincide', 'incorrecto', 'inválido', 'invalido'];

        foreach ($patterns as $pattern) {
            if (stripos($text, $pattern) !== false) {
                Log::info("SENIAT: Código captcha incorrecto (\"{$pattern}\").");
                throw new \RuntimeException("El código captcha ingresado es incorrecto.");
            }
        }
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
