<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

trait HandlesIndexOptions
{
    protected array $indexDefaults = [
        'search' => '',
        'filters' => [],
        'perPage' => 15,
        'sortField' => 'name',
        'sortOrder' => 1,
    ];

    protected function resolveIndexOptions(Request $request): array
    {
        return [
            'search' => $request->input('search', $this->indexDefaults['search']),
            'filters' => $request->input('filters', $this->indexDefaults['filters']),
            'perPage' => (int) $request->input('perPage', $this->indexDefaults['perPage']),
            'sortField' => $request->input('sortField', $this->indexDefaults['sortField']),
            'sortOrder' => (int) $request->input('sortOrder', $this->indexDefaults['sortOrder']),
        ];
    }
}
