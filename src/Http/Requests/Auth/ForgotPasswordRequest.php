<?php

namespace Fintech\RestApi\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            config('fintech.auth.auth_field', 'login_id')
            => config('fintech.auth.auth_field_rules', ['required', 'string', 'min:6', 'max:255'])
        ];
    }
}
