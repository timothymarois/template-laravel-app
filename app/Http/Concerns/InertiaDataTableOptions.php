<?php

declare(strict_types=1);

namespace App\Http\Concerns;

use App\Support\Caster;
use Illuminate\Http\Request;

/**
 * Consuming controllers provide these properties:
 *   - array $filterCasts    (required) — map of filter key => cast type for Caster.
 *   - array $indexDefaults  (optional) — default search/filters/perPage/sort values.
 *   - array $sessionStoreKeys (optional) — request keys persisted to the session.
 */
trait InertiaDataTableOptions
{
    /**
     * Resolve datatable options from the request, with optional session persistence.
     */
    public function resolveIndexOptions(Request $request, bool $withSession = false, ?string $sessionKey = null): array
    {
        $sessKey = $sessionKey ?? $request->route()?->getName();

        $defaults = [
            'search' => $this->indexDefaults['search'] ?? '',
            'filters' => $this->indexDefaults['filters'] ?? [],
            'viewFields' => $this->indexDefaults['viewFields'] ?? [],
            'perPage' => $this->indexDefaults['perPage'] ?? 15,
            'sortField' => $this->indexDefaults['sortField'] ?? 'id',
            'sortOrder' => (int) ($this->indexDefaults['sortOrder'] ?? 1),
        ];

        if ($request->isMethod('POST') && $withSession && $sessKey) {
            $payload = $request->only($this->getSessionStoreKeys());
            $this->storeSessionData($request, $sessKey, $payload);

            return $payload;
        }

        $sessionData = $withSession && $sessKey
            ? $this->resolveSessionData($request, $defaults, $sessKey)
            : [];

        $input = $request->only($this->getSessionStoreKeys());

        $merged = array_merge($defaults, $sessionData, $input);

        $merged['sortOrder'] = (int) ($merged['sortOrder'] ?? $defaults['sortOrder']);
        $merged['perPage'] = (int) ($merged['perPage'] ?? $defaults['perPage']);

        /** @var array<string, string> $filterCasts */
        $filterCasts = $this->filterCasts;
        $merged['filters'] = Caster::cast($merged['filters'], $filterCasts);

        return $merged;
    }

    protected function getSessionStoreKeys(): array
    {
        return property_exists($this, 'sessionStoreKeys')
            ? $this->sessionStoreKeys
            : ['search', 'filters', 'viewFields', 'perPage', 'sortField', 'sortOrder'];
    }

    protected function resolveSessionData(Request $request, array $defaults, string $sessionKey): ?array
    {
        return session("options.{$sessionKey}", []);
    }

    protected function storeSessionData(Request $request, string $sessionKey, array $payload): void
    {
        session()->put("options.{$sessionKey}", $payload);
    }
}
