<?php

namespace App\Http\Controllers;

use App\Services\Scraping\EmployeeDataScraper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScraperController extends Controller
{
    /**
     * Busca datos de un empleado por cédula y fecha de nacimiento
     * en fuentes externas (IVSS, SENIAT).
     *
     * POST /api/scrape/employee
     *
     * Body: { ci: "16456780", birthdate: "20/09/1990" }
     */
    public function scrapeEmployee(Request $request, EmployeeDataScraper $scraper): JsonResponse
    {
        $validated = $request->validate([
            'ci'        => ['required', 'string', 'min:5', 'max:15'],
            'birthdate' => ['required', 'string', 'min:8', 'max:10'],
        ]);

        try {
            $result = $scraper->fetch($validated['ci'], $validated['birthdate']);

            $statusCode = $result['success'] ? 200 : 422;

            return response()->json($result, $statusCode);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data'    => null,
                'source'  => 'error',
                'error'   => 'Error interno al procesar la búsqueda: ' . $e->getMessage(),
            ], 500);
        }
    }
}
