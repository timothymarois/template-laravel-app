<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Carbon;

class Caster
{
    /**
        * Cast the given data based on the provided cast definitions.
        */
    public static function cast(array $data, array $casts): array
    {
        foreach ($casts as $key => $type) {
            if (array_key_exists($key, $data)) {
                $data[$key] = self::applyCast($data[$key], $type);
            }
        }

        return $data;
    }

    private static function applyCast(mixed $value, string $type): mixed
    {
        return match ($type) {
            'int', 'integer' => (int) $value,
            'float', 'double' => (float) $value,
            'string' => (string) $value,
            'bool', 'boolean' => self::castToBoolean($value),
            'array' => (array) $value,
            'datetime' => self::castToDateTime($value),
            'json' => self::castToJson($value),
            default => $value,
        };
    }

    private static function castToBoolean(mixed $value): bool
    {
        if (is_string($value)) {
            return match (strtolower($value)) {
                'true', '1', 'yes' => true,
                'false', '0', 'no' => false,
                default => (bool) $value,
            };
        }

        return (bool) $value;
    }

    private static function castToDateTime(mixed $value): ?Carbon
    {
        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    private static function castToJson(mixed $value): mixed
    {
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }
        }

        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }
}
