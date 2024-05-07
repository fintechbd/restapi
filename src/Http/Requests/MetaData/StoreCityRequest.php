<?php

namespace Fintech\RestApi\Http\Requests\MetaData;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCityRequest extends FormRequest
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
            'name' => ['string', 'required', 'min:3'],
            'latitude' => ['numeric', 'nullable'],
            'longitude' => ['numeric', 'nullable'],
            'enabled' => ['boolean', 'nullable'],
            'city_data' => ['array', 'nullable'],
            'vendor_code' => ['nullable', 'array'],
            'state_id' => ['integer', 'required', 'min:1'],
            'country_id' => ['integer', 'required', 'min:1'],
        ];
    }
}
