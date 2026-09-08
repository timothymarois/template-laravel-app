<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListUsersRequest extends FormRequest
{
    /** Only an operator who may see the admin user list may read it over the API. */
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', User::class) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'string', 'max:255'],
            'page' => ['sometimes', 'integer', 'min:1'],
            // Bounded on purpose. An unbounded perPage lets one key ask for every row
            // in the table and turns a read endpoint into a denial of service.
            'perPage' => ['sometimes', 'integer', 'min:1', 'max:100'],
            // Allow-listed, not free text: sortField reaches orderBy(), so an arbitrary
            // value is both a 500 on an unknown column and an injection surface.
            'sortField' => ['sometimes', Rule::in(['id', 'name', 'email', 'created_at'])],
            'sortOrder' => ['sometimes', Rule::in([1, -1, '1', '-1'])],
        ];
    }
}
