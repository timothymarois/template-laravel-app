<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    /**
     * `Password::defaults()` is configured in AppServiceProvider (min 8 everywhere,
     * plus letters/mixedCase/numbers/symbols in production). Registration uses the
     * same rule, so a reset can never set a password registration would reject.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'lowercase', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
