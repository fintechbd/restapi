<?php

namespace Fintech\RestApi\Http\Requests\Banco;

use Illuminate\Foundation\Http\FormRequest;

class StoreBeneficiaryAccountTypeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'bank_id' => ['required', 'integer', 'min:1'],
            'name' => ['required', 'string'],
            'slug' => ['required', 'string'],
            'enabled' => ['nullable', 'boolean'],
            'beneficiary_account_types_data' => ['nullable', 'array'],
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
