<?php

namespace Fintech\RestApi\Http\Requests\Banco;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImportBeneficiaryTypeRequest extends FormRequest
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
        $uniqueRule = 'unique:beneficiary_types,beneficiary_type_name';

        return [
            'beneficiary_type_name' => ['required', 'string', 'max:255', $uniqueRule],
            'beneficiary_type_data' => ['required', 'array'],
            'beneficiary_type_data.*.beneficiary_type_condition_name' => ['nullable', 'string'],
            'beneficiary_type_data.*.beneficiary_type_condition_field_name' => ['nullable', 'string'],
            'beneficiary_type_data.*.beneficiary_type_condition_field_type' => ['nullable', 'string'],
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
