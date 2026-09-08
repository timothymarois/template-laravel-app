<?php

declare(strict_types=1);

namespace App\Enums;

use ArchTech\Enums\Values;

/**
 * The closed set of abilities an API key may carry.
 *
 * Abilities are an enum rather than free text so a key can never be minted with a
 * scope nothing checks: `ApiKeyService` validates against these cases, and the
 * `abilities:` middleware on a route names one of them. A fork adds a case here
 * and the route that enforces it in the same change — an ability with no
 * enforcing route is decoration, and a route with no ability is unscoped.
 */
enum ApiAbility: string
{
    use Values;

    /** Read the authenticated identity and other read-only resources. */
    case Read = 'api:read';

    /** Create or modify resources. */
    case Write = 'api:write';

    public function label(): string
    {
        return match ($this) {
            self::Read => 'Read',
            self::Write => 'Write',
        };
    }
}
