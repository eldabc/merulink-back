<?php

namespace App\Services\Scraping;

use Illuminate\Support\Facades\Log;

class SeniatScraper extends BaseScraper
{
    public function scrape(string $ci, string $birthdate): array
    {
        Log::info("SENIAT Scraper: No disponible aún.");
        throw new \RuntimeException("SENIAT no está disponible en este momento.");
    }
}
