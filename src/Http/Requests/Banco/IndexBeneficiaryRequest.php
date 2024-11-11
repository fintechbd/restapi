<?php

namespace Fintech\RestApi\Http\Requests\Banco;

use Fintech\RestApi\Traits\HasPaginateQuery;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexBeneficiaryRequest extends FormRequest
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
            'search' => ['string', 'nullable', 'max:255'],
            'per_page' => ['integer', 'nullable', 'min:10', 'max:500'],
            'page' => ['integer', 'nullable', 'min:1'],
            'paginate' => ['boolean'],
            'sort' => ['string', 'nullable', 'min:2', 'max:255'],
            'dir' => ['string', 'min:3', 'max:4'],
            'trashed' => ['boolean', 'nullable'],
            'user_id' => ['nullable', 'integer'],
            'city_id' => ['nullable', 'integer'],
            'state_id' => ['nullable', 'integer'],
            'country_id' => ['nullable', 'integer'],
            'relation_id' => ['nullable', 'integer'],
            'beneficiary_type_id' => ['nullable', 'integer'],
            'beneficiary_name' => ['nullable', 'string'],
            'beneficiary_mobile' => ['nullable', 'string', 'min:8', 'max:16', 'regex:/[0-9]{9}/'],
            'beneficiary_address' => ['nullable', 'string'],
            'beneficiary_data' => ['nullable', 'array'],
            'enabled' => ['nullable', 'boolean'],
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
