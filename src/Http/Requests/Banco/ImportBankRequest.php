<?php

namespace Fintech\RestApi\Http\Requests\Banco;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImportBankRequest extends FormRequest
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
        /** @phpstan-ignore-next-line */
        $bank_id = (int) collect(request()->segments())->last(); //id of the resource
        $uniqueRule = 'unique:banks,bank_name,'.$bank_id.',id,deleted_at,NULL';

        return [
            'country_id' => ['required', 'integer'],
            'beneficiary_type_id' => ['required', 'integer'],
            'bank_name' => ['required', 'string', 'max:255', $uniqueRule],
            'bank_category' => ['required', 'string', 'max:255'],
            'transaction_type' => ['nullable', 'string', 'max:255'],
            'bank_currency' => ['required', 'string', 'min:3', 'max:3'],
            'logo_png' => ['nullable', 'string'],
            'logo_svg' => ['nullable', 'string'],
            'bank_data' => ['nullable', 'array'],
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
