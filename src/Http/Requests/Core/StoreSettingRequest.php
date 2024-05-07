<?php

namespace Fintech\RestApi\Http\Requests\Core;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
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
        return [
            'user_id' => ['nullable', 'integer', 'min:1'],
            'package' => ['string', 'required', 'min:3', 'max:255'],
            'label' => ['string', 'required', 'min:5', 'max:255'],
            'description' => ['string', 'required', 'min:5', 'max:255'],
            'key' => ['string', 'required', 'min:3', 'max:255'],
            'type' => ['string', 'required', 'in:null,string,boolean,integer,double,array,json,object'],
            'value' => ['nullable'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'type' => strtolower($this->input('type', 'string')),
        ]);
    }
}
