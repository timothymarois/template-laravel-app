<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ApiAbility;
use App\Models\User;
use App\Services\DataTransferObjects\IssuedApiKey;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Issue, list and revoke API keys on Sanctum personal access tokens.
 *
 * Three invariants, each of which has to hold here because nothing downstream can
 * restore it:
 *
 *   - Abilities come from ApiAbility, never from request input. A caller that could
 *     send its own abilities list could widen its own scope.
 *   - A key expires unless the issuer deliberately says otherwise. NEVER_EXPIRES
 *     has to be passed explicitly; omitting the lifetime gives the default, not a
 *     permanent key. Both paths check the date: Sanctum's guard rejects an
 *     expired token, and `EnsureApiKey` checks `expires_at` again because it does its
 *     own `PersonalAccessToken::findToken()` lookup, which never goes through the
 *     guard. That second check is load-bearing, not redundant — do not delete it.
 *   - The plaintext is returned exactly once, in an IssuedApiKey, and is never
 *     stored, logged or flashed. Only its SHA-256 hash persists.
 *
 * Revocation is a hard delete, not a soft one: a request presenting a revoked key
 * afterwards must authenticate as nobody rather than as a downgraded user.
 */
class ApiKeyService
{
    /** Longest life a key may be given, in days. */
    public const MAX_LIFETIME_DAYS = 365;

    /** Life a key is given when the caller does not choose one. */
    public const DEFAULT_LIFETIME_DAYS = 90;

    /**
     * Pass as $lifetimeDays for a key that never expires.
     *
     * Deliberately not the default and deliberately not `null`: a non-expiring
     * credential is a real decision — the kind of key that outlives the person who
     * created it — so it has to be asked for by name rather than fallen into by
     * omitting an argument.
     */
    public const NEVER_EXPIRES = 0;

    /**
     * @param  array<int, string>  $abilities
     * @param  int|null  $lifetimeDays  Days until expiry; NEVER_EXPIRES for a key with
     *                                  no expiry; null for DEFAULT_LIFETIME_DAYS.
     *
     * @throws InvalidArgumentException when an ability is outside ApiAbility, or the
     *                                  lifetime is neither NEVER_EXPIRES nor within
     *                                  1..MAX_LIFETIME_DAYS.
     */
    public function issue(User $owner, string $name, array $abilities, ?int $lifetimeDays = null): IssuedApiKey
    {
        $days = $lifetimeDays ?? self::DEFAULT_LIFETIME_DAYS;

        if ($days !== self::NEVER_EXPIRES && ($days < 1 || $days > self::MAX_LIFETIME_DAYS)) {
            throw new InvalidArgumentException('An API key lifetime must be '.self::NEVER_EXPIRES.' (never) or between 1 and '.self::MAX_LIFETIME_DAYS.' days.');
        }

        if ($abilities === []) {
            throw new InvalidArgumentException('An API key must carry at least one ability.');
        }

        $allowed = ApiAbility::values();

        foreach ($abilities as $ability) {
            if (! in_array($ability, $allowed, true)) {
                throw new InvalidArgumentException("Unknown API ability [{$ability}].");
            }
        }

        $token = $owner->createToken(
            name: $name,
            abilities: array_values(array_unique($abilities)),
            expiresAt: $days === self::NEVER_EXPIRES ? null : Carbon::now()->addDays($days),
        );

        /** @var PersonalAccessToken $key */
        $key = $token->accessToken;

        return new IssuedApiKey($key, $token->plainTextToken);
    }

    /**
     * @return Collection<int, PersonalAccessToken>
     */
    public function listFor(User $owner): Collection
    {
        /** @var Collection<int, PersonalAccessToken> $keys */
        $keys = $owner->tokens()->latest()->get();

        return $keys;
    }

    /**
     * Revoke one key belonging to the given owner.
     *
     * Ownership is re-checked here rather than trusted from the route, so a guessed
     * id cannot revoke somebody else's key.
     */
    public function revoke(User $owner, int $keyId): bool
    {
        return $owner->tokens()->whereKey($keyId)->delete() > 0;
    }

    /**
     * Revoke every key belonging to the owner.
     *
     * Nothing in the template calls this: deactivating a user is already enforced
     * at request time by EnsureApiKey, which refuses a key whose owner is inactive.
     * It is here for a fork that wants deactivation to destroy the keys outright
     * rather than merely refuse them.
     */
    public function revokeAll(User $owner): int
    {
        return $owner->tokens()->delete();
    }
}
