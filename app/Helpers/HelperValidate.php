<?php

namespace App\Helpers;

class HelperValidate
{
    /**
     * Check if string is valid JSON
     *
     * @param string|null $string
     * @return boolean
     */
    public static function isJson(string|null $string): bool
    {
        if (! $string) {
            return false;
        }
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }
}
