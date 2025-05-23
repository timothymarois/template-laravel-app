<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

trait HandlesIndexOptions
{
    public function resolveIndexOptions(Request $request): array
    {
        return [
            'search' => $request->input('search', $this->indexDefaults['search'] ?? ''),
            'filters' => $request->input('filters', $this->indexDefaults['filters'] ?? []),
            'perPage' => (int) $request->input('perPage', $this->indexDefaults['perPage'] ?? 15),
            'sortField' => $request->input('sortField', $this->indexDefaults['sortField'] ?? 'id'),
            'sortOrder' => (int) $request->input('sortOrder', $this->indexDefaults['sortOrder'] ?? 1),
        ];
    }
}

