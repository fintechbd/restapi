<?php

namespace Fintech\RestApi\Http\Requests\Business;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImportChargeBreakDownRequest extends FormRequest
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
            'service_stat_id' => ['integer', 'required'],
            'service_slug' => ['string', 'required'],
            'charge_break_down_lower' => ['numeric', 'required'],
            'charge_break_down_higher' => ['numeric', 'required'],
            'charge_break_down_charge' => ['string', 'required'],
            'charge_break_down_discount' => ['string', 'required'],
            'charge_break_down_commission' => ['string', 'required'],
            'enabled' => ['boolean', 'nullable', 'min:1'],
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
