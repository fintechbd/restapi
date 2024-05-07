<?php

namespace Fintech\RestApi\Http\Requests\Banco;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBankRequest extends FormRequest
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
        $uniqueRule = 'unique:banks,name,'.$this->route('bank').',id,country_id,'.$this->input('country_id').',deleted_at,null';

        return [
            'country_id' => ['required', 'integer'],
            'beneficiary_types' => ['nullable', 'array'],
            'beneficiary_types.*' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255', $uniqueRule],
            'category' => ['required', 'string', 'max:255'],
            'transaction_type' => ['nullable', 'string', 'max:255'],
            'currency' => ['required', 'string', 'min:3', 'max:3'],
            'logo_png' => ['nullable', 'string'],
            'logo_svg' => ['nullable', 'string'],
            'bank_data' => ['nullable', 'array'],
            'vendor_code' => ['nullable', 'array'],
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
