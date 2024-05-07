<?php

namespace Fintech\RestApi\Http\Requests\Business;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceFieldRequest extends FormRequest
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
        $rules = [
            'service_id' => ['integer', 'required'],
            'name' => ['string', 'required'],
            'label' => ['string', 'required'],
            'type' => ['string', 'required'],
            'hint' => ['string', 'nullable'],
            'options' => ['array', 'nullable'],
            'value' => ['string', 'nullable'],
            'required' => ['boolean', 'nullable'],
            'reserved' => ['boolean', 'nullable'],
            'enabled' => ['boolean', 'nullable'],
            'validation' => ['string', 'nullable'],
            'service_field_data' => ['array', 'nullable'],
            'service_field_data.wrapper' => ['array', 'nullable'],
            'service_field_data.class' => ['string', 'nullable'],
            'service_field_data.style' => ['string', 'nullable'],
        ];

        /*Business::serviceSetting()->list([
            'paginate' => false,
            'service_setting_type' => 'service_field',
        ])->each(function ($serviceSetting) use (&$rules) {
            $validation = $serviceSetting->service_setting_rule ?? 'string|nullable';
            $rules["service_field_data.{$serviceSetting->service_setting_field_name}"] = explode('|', $validation);
        });*/

        return $rules;
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
