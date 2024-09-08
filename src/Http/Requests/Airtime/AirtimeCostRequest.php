<?php

namespace Fintech\RestApi\Http\Requests\Airtime;

use Fintech\Core\Rules\MobileNumber;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AirtimeCostRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'min:1'],
            'service_id' => ['required', 'integer', 'min:1'],
            'source_country_id' => ['required', 'integer', 'min:1'],
            'destination_country_id' => ['required', 'integer', 'min:1'],
            'reverse' => ['required', 'boolean'],
            'reload' => ['nullable', 'boolean'],
            'airtime_data' => ['nullable', 'array'],
            'airtime_data.recipient_msisdn' => ['required', new MobileNumber],
            'airtime_data.amount' => ['required', 'integer', 'min:1'],
            'airtime_data.connection_type' => ['required', 'integer', 'min:1'],
            'airtime_data.operator_short_code' => ['required', 'integer', 'min:1'],
        ];
    }
}
