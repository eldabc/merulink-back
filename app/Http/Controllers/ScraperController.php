<?php

namespace App\Http\Controllers;

use App\Services\Scraping\EmployeeDataScraper;
use App\Services\Scraping\SeniatScraper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScraperController extends Controller
{
    /**
     * Busca datos del empleado en IVSS (y opcionalmente SENIAT con captcha).
     *
     * POST /api/scrape/employee
     * Body: { ci, birthdate, seniat_code? }
     */
    public function scrapeEmployee(Request $request, EmployeeDataScraper $scraper): JsonResponse
    {
        $validated = $request->validate([
            'ci'            => ['required', 'string', 'min:5', 'max:15'],
            'birthdate'     => ['required', 'string', 'min:8', 'max:10'],
            'seniat_code' => ['nullable', 'string', 'max:10'],
        ]);

        $result = $scraper->fetch(
            $validated['ci'],
            $validated['birthdate'],
            $validated['seniat_code'] ?? null
        );

        $statusCode = $result['success'] ? 200 : 422;

        return response()->json($result, $statusCode);
    }

    /**
     * Obtiene la imagen captcha del SENIAT para que el usuario la resuelva.
     *
     * POST /api/scrape/seniat/captcha
     * Body: { ci }
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
