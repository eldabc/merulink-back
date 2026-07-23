<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponseHelper
{
    /**
     * Crea una respuesta JSON estandarizada para la API.
     *
     * @param  string  $status   'ok' o 'fail'
     * @param  string  $code     Código interno de la operación (ej: 'created_role', 'updated_role')
     * @param  string  $message  Mensaje legible para el usuario
     * @param  mixed   $data     Datos adicionales (opcional)
     * @param  int     $httpCode Código HTTP (opcional, por defecto 200)
     * @return JsonResponse
     */
    public static function createResponse(
        string $status,
        string $code,
        string $message,
        mixed $data = null,
        int $httpCode = 200
    ): JsonResponse {
        $response = [
            'status'    => $status,
            'code'      => $code,
            'message'   => $message,
            'timestamp' => now()->toIso8601String(),
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $httpCode);
    }
}
