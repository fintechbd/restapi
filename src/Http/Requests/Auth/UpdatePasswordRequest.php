<?php

namespace Fintech\RestApi\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current' => [
                ...config('fintech.auth.password_field_rules', ['required', 'string', 'min:8']),
                'current_password'
            ],
            config('fintech.auth.password_field', 'password') => [
                ...config('fintech.auth.password_field_rules', ['required', 'string', 'min:8']),
                'confirmed', 'different:current'
            ]
        ];
    }
}
