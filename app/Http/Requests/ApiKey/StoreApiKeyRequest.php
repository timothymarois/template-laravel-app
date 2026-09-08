<?php

declare(strict_types=1);

namespace App\Http\Requests\ApiKey;

use App\Enums\ApiAbility;
use App\Services\ApiKeyService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\PersonalAccessToken;

class StoreApiKeyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PersonalAccessToken::class) === true;
    }

    /**
     * `abilities` is validated against the enum rather than accepted as free text:
     * an ability nothing enforces is not a scope. `lifetime_days` is bounded here
     * and again in ApiKeyService, which is the one that must hold — the service is
     * reachable from a command or a job that never passes through this request.
     *
     * `lifetime_days` of 0 means never expires (ApiKeyService::NEVER_EXPIRES), which
     * is why the minimum is 0 and not 1; omitting the field gives the default life,
     * so a non-expiring key can only be created by asking for one.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'abilities' => ['required', 'array', 'min:1'],
            'abilities.*' => ['required', 'string', Rule::enum(ApiAbility::class)],
            'lifetime_days' => ['nullable', 'integer', 'min:'.ApiKeyService::NEVER_EXPIRES, 'max:'.ApiKeyService::MAX_LIFETIME_DAYS],
        ];
    }
}
