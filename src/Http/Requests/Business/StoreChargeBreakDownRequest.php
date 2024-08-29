<?php

namespace Fintech\RestApi\Http\Requests\Business;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreChargeBreakDownRequest extends FormRequest
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
            'service_id' => ['integer', 'required'],
            'lower_limit' => ['numeric', 'required'],
            'higher_limit' => ['numeric', 'required'],
            'charge' => ['string', 'required'],
            'discount' => ['string', 'required'],
            'commission' => ['string', 'required'],
            'enabled' => ['boolean', 'nullable'],
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
