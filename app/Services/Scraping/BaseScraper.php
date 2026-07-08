<?php

namespace App\Services\Scraping;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;

abstract class BaseScraper
{
    protected Client $httpClient;
    protected CookieJar $cookieJar;
    protected int $timeout = 30;

    public function __construct()
    {
        $this->cookieJar = new CookieJar();
        $this->httpClient = new Client([
            'timeout'         => $this->timeout,
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

    abstract public function scrape(string $ci, string $birthdate): array;

    protected function normalizeCi(string $ci): string
    {
        return trim(strtoupper($ci), 'V-E- ');
    }

    protected function formatBirthdate(string $birthdate): string
    {
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $birthdate)) {
            return $birthdate;
        }
        try {
            return (new \DateTime($birthdate))->format('d/m/Y');
        } catch (\Exception $e) {
            return $birthdate;
        }
    }
}
