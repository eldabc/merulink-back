<?php

namespace App\Helpers;

class StringHelper
{
    /**
     * Normaliza un string para usar como nombre interno:
     * minúsculas, sin espacios, espacios reemplazados por guiones.
     *
     * @param  string  $value
     * @return string
     */
    public static function slugify(string $value): string
    {
        return preg_replace('/\s+/', '-', trim(strtolower($value)));
    }

    /**
     * Capitaliza solo la primera letra; el resto en minúsculas.
     * Ej: "MARÍA DE LOS" → "María de los"
     *
     * @param  string|null  $value
     * @return string|null
     */
    public static function capitalize(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        // El SENIAT antepone &nbsp; (\u00A0): trim() no lo quita
        // convertir a espacio normal antes de normalizar.
        $value = preg_replace('/\x{00A0}/u', ' ', $value);
        $value = mb_strtolower(trim($value));

        return mb_strtoupper(mb_substr($value, 0, 1)) . mb_substr($value, 1);
    }
}
