<?php

namespace Fintech\RestApi\Http\Requests\Business;

use Fintech\Business\Facades\Business;
use Fintech\Core\Rules\ChargeHigherLimit;
use Fintech\Core\Rules\ChargeLowerLimit;
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
        $serviceStat = Business::serviceStat()->find($this->input(['service_stat_id']));

        $stat_lower_limit = floatval($serviceStat->service_stat_data[0]['lower_limit']);
        $stat_higher_limit = floatval($serviceStat->service_stat_data[0]['higher_limit']);

        return [
            'service_stat_id' => ['integer', 'required'],
            'service_id' => ['integer', 'required'],
            'lower_limit' => ['numeric', 'required', "gte:{$stat_lower_limit}", "lte:{$stat_higher_limit}", new ChargeLowerLimit],
            'higher_limit' => ['numeric', 'required', "gte:{$stat_lower_limit}", "lte:{$stat_higher_limit}", new ChargeHigherLimit],
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
