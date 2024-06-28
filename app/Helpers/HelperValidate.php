<?php

namespace App\Helpers;

class HelperValidate
{
    /**
     * Check if string is valid JSON
     */
    public static function isJson(?string $string): bool
    {
        if (! $string) {
            return false;
        }
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }
}
