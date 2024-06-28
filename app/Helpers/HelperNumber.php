<?php

namespace App\Helpers;

class HelperNumber
{
    /**
     * Convert a true/false string to boolean
     * https://stackoverflow.com/questions/60073270/passing-boolean-param-in-laravel-via-url
     */
    public static function convertBoolean($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    /**
     * Convert byte size (kb/mb/gb etc)
     */
    public static function convertBytesToReadable(float $size): string
    {
        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];

        return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2).''.$unit[$i];
    }

    /**
     * Number to the nearest 10
     * Example: 106 = 110
     */
    public static function nearestTen(int $value): int
    {
        return (int) ceil($value / 10) * 10;
    }

    /**
     * Generate a readable id
     * Example: G1XFUU5TPW
     *
     * While the number of combinations are quite large,
     * do not use this for a true uniqueness id
     */
    public static function generateRandomId(int $length = 10, string $characters = '1234567890ABCDEFGHJKLIMNPQORSTUVWXYZ')
    {
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    /**
     * Generate a unique number id
     * Example: 1777131738217312
     * Example: 122128550296537714163848213 (with entropy)
     */
    public static function generateNumberId(bool $entropy = false)
    {
        $hex = str_replace('.', '', (string) uniqid('', $entropy));
        $bchexdec = function ($hex) use (&$bchexdec) {
            if (strlen($hex) == 1) {
                return (string) hexdec($hex);
            } else {
                $remain = substr($hex, 0, -1);
                $last = substr($hex, -1);

                return bcadd(bcmul('16', $bchexdec($remain)), (string) hexdec($last));
            }
        };

        return $bchexdec($hex);
    }
}
