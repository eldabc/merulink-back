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
}
