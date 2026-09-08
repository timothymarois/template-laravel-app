<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    /**
     * Deliberately no `exists:users` rule — it would return a field error for an
     * unregistered address and defeat the controller's enumeration contract.
     * Rules mirror LoginRequest, so the address that can log in is the address
     * that can request a reset.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|lowercase|max:255',
        ];
    }
}
