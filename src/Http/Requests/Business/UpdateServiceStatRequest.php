<?php

namespace Fintech\RestApi\Http\Requests\Business;

use Fintech\Core\Rules\PercentNumber;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceStatRequest extends FormRequest
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
            'role_id' => ['integer', 'required'],
            'service_id' => ['integer', 'required'],
            'service_slug' => ['string', 'required'],
            'source_country_id' => ['integer', 'required'],
            'destination_country_id' => ['integer', 'required'],
            'service_vendor_id' => ['integer', 'required'],
            'service_stat_data' => ['array', 'required'],
            'service_stat_data.*.lower_limit' => ['string', 'required', 'numeric'],
            'service_stat_data.*.higher_limit' => ['string', 'required', 'numeric'],
            'service_stat_data.*.local_currency_higher_limit' => ['string', 'required', 'numeric'],
            'service_stat_data.*.charge' => ['string', 'required', new PercentNumber],
            'service_stat_data.*.discount' => ['string', 'required', new PercentNumber],
            'service_stat_data.*.commission' => ['string', 'required', new PercentNumber],
            'service_stat_data.*.cost' => ['string', 'required', new PercentNumber],
            'service_stat_data.*.charge_refund' => ['string', 'required', 'in:yes,no'],
            'service_stat_data.*.discount_refund' => ['string', 'required', 'in:yes,no'],
            'service_stat_data.*.commission_refund' => ['string', 'required', 'in:yes,no'],
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
