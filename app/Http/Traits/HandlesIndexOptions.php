<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

trait HandlesIndexOptions
{
    // apply on controller where needed
    // protected array $filterCasts = [
    //     'user_id' => 'int',
    // ];

    public function resolveIndexOptions(Request $request): array
    {
        $filters = $request->input('filters', $this->indexDefaults['filters'] ?? []);

        if (property_exists($this, 'filterCasts') && is_array($this->filterCasts)) {
            foreach ($this->filterCasts as $key => $type) {
                if (isset($filters[$key])) {
                    $filters[$key] = match ($type) {
                        'int' => (int) $filters[$key],
                        'bool' => filter_var($filters[$key], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE),
                        default => $filters[$key],
                    };
                }
            }
        }

        return [
            'search' => $request->input('search', $this->indexDefaults['search'] ?? ''),
            'filters' => $filters,
            'perPage' => (int) $request->input('perPage', $this->indexDefaults['perPage'] ?? 15),
            'sortField' => $request->input('sortField', $this->indexDefaults['sortField'] ?? 'id'),
            'sortOrder' => (int) $request->input('sortOrder', $this->indexDefaults['sortOrder'] ?? 1),
        ];
    }
}
