<?php

namespace Fintech\RestApi\Http\Requests\MetaData;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStateRequest extends FormRequest
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
            'name' => ['string', 'required'],
            'type' => ['string', 'nullable'],
            'latitude' => ['numeric', 'nullable'],
            'longitude' => ['numeric', 'nullable'],
            'enabled' => ['bool', 'nullable'],
            'state_data' => ['array', 'nullable'],
            'vendor_code' => ['nullable', 'array'],
            'country_id' => ['integer', 'required', 'min:1'],
        ];
    }
}
