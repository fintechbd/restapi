<?php

namespace Fintech\RestApi\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePermissionRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $uniqueRule = 'unique:permissions,name';

        return [
            'name' => ['required', 'string', 'min:5', 'max:255', $uniqueRule],
            'guard_name' => ['required', 'string', Rule::in(array_keys(config('auth.guards', ['web', 'api'])))],
        ];
    }
}
