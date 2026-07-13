<?php

namespace App\Http\Controllers;

use App\Services\Scraping\EmployeeDataScraper;
use App\Services\Scraping\SeniatScraper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ScrapeEmployeeRequest;

class ScraperController extends Controller
{
    /**
     * Ejecuta scraper según el source
     *
     * POST /api/scrape/employee
     * Body: { source: "ivss"|"seniat", ci, birthdate?, seniat_code? }
     */
    public function scrapeEmployee(ScrapeEmployeeRequest $request, EmployeeDataScraper $scraper): JsonResponse
    {

        $validated = $request->validated();

        $result = $scraper->fetchBySource(
            $validated['source'],
            $validated['ci'],
            $validated['birthdate'] ?? '',
            $validated['seniat_code'] ?? null
        );

        $statusCode = ($result['success'] ?? false) ? 200 : 422;

        return response()->json($result, $statusCode);
    }

    /**
     * Obtiene la imagen captcha del SENIAT.
     *
     * POST /api/scrape/seniat/captcha
     */
    public function getSeniatCaptcha(Request $request, SeniatScraper $seniat): JsonResponse
    {
        try {
            $captchaData = $seniat->getCaptcha();

            return response()->json([
                'success'       => true,
                'captcha_image' => $captchaData['captcha'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => 'No se pudo obtener el captcha del SENIAT: ' . $e->getMessage(),
            ], 500);
        }
    }
}
