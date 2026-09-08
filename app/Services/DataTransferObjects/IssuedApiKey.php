<?php

declare(strict_types=1);

namespace App\Services\DataTransferObjects;

use Laravel\Sanctum\PersonalAccessToken;

/**
 * An API key at the one moment its plaintext exists.
 *
 * Sanctum stores only a SHA-256 hash, so `$plainText` can never be recovered after
 * this object is discarded. It is returned to the screen that requested issuance
 * and must not be logged, flashed to the session, or written into Inertia history.
 */
final readonly class IssuedApiKey
{
    public function __construct(
        public PersonalAccessToken $key,
        public string $plainText,
    ) {}
}
