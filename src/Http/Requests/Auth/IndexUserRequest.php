<?php

namespace Fintech\RestApi\Http\Requests\Auth;

use Fintech\RestApi\Traits\HasPaginateQuery;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexUserRequest extends FormRequest
{
    use HasPaginateQuery;

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
        return [
            'roles' => ['array', 'nullable', 'min:1'],
            'role_name' => ['string', 'nullable', 'max:255'],
            'search' => ['string', 'nullable', 'max:255'],
            'mobile' => ['string', 'nullable', 'min:10'],
            'wallet_recipient' => ['string', 'nullable', 'min:10'],
            'per_page' => ['integer', 'nullable', 'min:10', 'max:500'],
            'agent_id' => ['integer', 'nullable', 'min:1'],
            'user_id' => ['integer', 'nullable', 'min:1'],
            'id_in' => ['integer', 'nullable', 'min:1'],
            'id_not_in' => ['integer', 'nullable', 'min:1'],
            'parent_id' => ['integer', 'nullable', 'min:1'],
            'email' => ['string', 'email:dns,rfs', 'min:5'],
            'present_country_id' => ['integer', 'nullable', 'min:1'],
            'page' => ['integer', 'nullable', 'min:1'],
            'paginate' => ['boolean'],
            'sort' => ['string', 'nullable', 'min:2', 'max:255'],
            'dir' => ['string', 'min:3', 'max:4'],
            'trashed' => ['boolean', 'nullable'],
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
