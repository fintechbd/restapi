<?php

namespace Fintech\RestApi\Http\Requests\MetaData;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreIdDocTypeRequest extends FormRequest
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
        $uniqueRule = 'unique:id_doc_types,code,null,country_id,'.$this->input('country_id');

        return [
            'country_id' => ['required', 'integer', 'min:1', 'max:255'],
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'code' => ['required', 'string', 'min:1', 'max:255'],
            'sides' => ['nullable', 'integer', 'min:1'],
            'enabled' => ['nullable', 'boolean'],
            'id_doc_type_data' => ['nullable', 'array'],
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
