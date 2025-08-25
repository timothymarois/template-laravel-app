<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

trait HandlesIndexOptions
{
    // override in controller
    // protected array $filterCasts = [
    //     'user_id' => 'int',
    // ];

    // override in controller
    // protected array $indexDefaults = [
    //     'perPage' => 50,
    //     'sortField' => 'name',
    //     'sortOrder' => 1,
    // ];

    // override in controller
    // protected array $sessionStoreKeys = [
    //     'search',
    //     'filters',
    //     'perPage',
    //     'sortField',
    //     'sortOrder',
    //     'filter_id',
    //     'viewFields',
    // ];

    public function resolveIndexOptions(Request $request, bool $withSession = false, ?string $sessionKey = null): array
    {
        $sessKey = $sessionKey ?? $request->route()?->getName();

        $defaults = [
            'search' => $this->indexDefaults['search'] ?? '',
            'filters' => $this->indexDefaults['filters'] ?? [],
            'viewFields' => $this->indexDefaults['viewFields'] ?? [],
            'perPage' => $this->indexDefaults['perPage'] ?? 15,
            'sortField' => $this->indexDefaults['sortField'] ?? 'id',
            'sortOrder' => (int) $this->indexDefaults['sortOrder'] ?? 1,
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

        $merged['sortOrder'] = (int) $merged['sortOrder'] ?? $defaults['sortOrder'];
        $merged['perPage'] = (int) $merged['perPage'] ?? $defaults['perPage'];

        if (property_exists($this, 'filterCasts') && is_array($this->filterCasts)) {
            foreach ($this->filterCasts as $key => $type) {
                if (isset($merged['filters'][$key])) {
                    $merged['filters'][$key] = match ($type) {
                        'int' => (int) $merged['filters'][$key],
                        'bool' => filter_var($merged['filters'][$key], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                        default => $merged['filters'][$key],
                    };
                }
            }
        }

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
