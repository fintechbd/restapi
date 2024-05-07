<?php

namespace Fintech\RestApi\Http\Requests\Business;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImportServiceRequest extends FormRequest
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
        $service_id = (int) collect(request()->segments())->last(); //id of the resource
        $uniqueRule = 'unique:services,service_slug,'.$service_id.',id,service_type_id,'.$this->input('service_type_id').',service_vendor_id,'.$this->input('service_vendor_id').',deleted_at,NULL';

        return [
            'service_type_id' => ['integer', 'required'],
            'service_vendor_id' => ['integer', 'required'],
            'service_name' => ['string', 'required', 'max:255'],
            'service_slug' => ['string', 'required', 'max:255', $uniqueRule],
            'service_notification' => ['string', 'nullable'],
            'service_delay' => ['string', 'nullable'],
            'service_stat_policy' => ['string', 'nullable'],
            'service_serial' => ['integer', 'required'],
            'logo_svg' => ['string', 'nullable'],
            'logo_png' => ['string', 'nullable'],
            'service_data' => ['array', 'required'],
            'service_data.*.visible_website' => ['string', 'nullable'],
            'service_data.*.visible_android_app' => ['string', 'nullable'],
            'service_data.*.visible_ios_app' => ['string', 'nullable'],
            'service_data.*.account_name' => ['string', 'nullable'],
            'service_data.*.account_number' => ['string', 'nullable'],
            'service_data.*.transactional_currency' => ['string', 'nullable'],
            'service_data.*.beneficiary_type_id' => ['integer', 'nullable'],
            'service_data.*.operator_short_code' => ['string', 'nullable'],
            'enabled' => ['boolean', 'nullable', 'min:1'],
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     */
    public function attributes(): array
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     */
    public function messages(): array
    {
        return [
            //
        ];
    }
}
