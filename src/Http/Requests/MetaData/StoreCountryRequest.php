<?php

namespace Fintech\RestApi\Http\Requests\MetaData;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCountryRequest extends FormRequest
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
            'iso2' => ['string', 'nullable'],
            'iso3' => ['string', 'nullable'],
            'phone_code' => ['string', 'nullable'],
            'capital' => ['string', 'nullable'],
            'currency' => ['string', 'nullable'],
            'currency_name' => ['string', 'nullable'],
            'currency_symbol' => ['string', 'nullable'],
            'nationality' => ['string', 'nullable'],
            'region_id' => ['integer', 'nullable'],
            'subregion_id' => ['integer', 'nullable'],
            'logo_svg' => ['string', 'nullable'],
            'logo_png' => ['string', 'nullable'],
            'enabled' => ['bool', 'nullable'],
            'emoji' => ['string', 'nullable'],
            'latitude' => ['numeric', 'nullable'],
            'longitude' => ['numeric', 'nullable'],
            'timezones' => ['array', 'nullable'],
            'country_data' => ['array', 'nullable'],
            'vendor_code' => ['nullable', 'array'],
            'country_data.language_enabled' => ['boolean', 'required'],
            'country_data.language_code' => ['string', 'min:2', 'required_if:language_enabled,true'],
            'country_data.language_name' => ['string', 'min:3', 'required_if:language_enabled,true'],
            'country_data.multi_currency_enabled' => ['boolean', 'required'],
            'country_data.is_serving' => ['boolean', 'required'],
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
