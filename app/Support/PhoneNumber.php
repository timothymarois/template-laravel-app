<?php

declare(strict_types=1);

namespace App\Support;

class PhoneNumber
{
    /**
     * Formats a 10-digit US phone number to (XXX) XXX-XXXX format.
     */
    public static function format(null|int|string $phoneNumber): ?string
    {
        if (! $phoneNumber) {
            return null;
        }

        $phoneNumber = (string) $phoneNumber;

        $phoneNumber = self::normalize($phoneNumber);

        if ($phoneNumber === null) {
            return null;
        }

        return '('.substr($phoneNumber, 0, 3).') '.
            substr($phoneNumber, 3, 3).'-'.
            substr($phoneNumber, 6);
    }

    /**
     * Normalizes a US phone number by stripping out country code, symbols, and formatting.
     */
    public static function normalize(string $phoneNumber): ?string
    {
        $phoneNumber = preg_replace('/[^+\\d]|(?<=\\+|1)[^0-9]/', '', $phoneNumber);

        if (strlen($phoneNumber) > 1 && $phoneNumber[0] === '+') {
            $phoneNumber = substr($phoneNumber, 1);
        }

        if (strlen($phoneNumber) === 11 && $phoneNumber[0] === '1') {
            $phoneNumber = substr($phoneNumber, 1);
        }

        return strlen($phoneNumber) === 10 ? $phoneNumber : null;
    }
}
