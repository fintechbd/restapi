<?php

namespace Fintech\RestApi\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
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
        $uniqueRule = 'unique:roles,name,' . $this->route('role') . ',id,deleted_at,NULL';

        return [
            'name' => ['required', 'string', 'min:5', 'max:255', $uniqueRule],
            'team_id' => ['nullable', 'integer'],
            'guard_name' => ['nullable', 'string', Rule::in(array_keys(config('auth.guards', ['web', 'api'])))],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['nullable', 'integer'],
        ];
    }
}
