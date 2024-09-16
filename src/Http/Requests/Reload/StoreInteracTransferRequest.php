<?php

namespace Fintech\RestApi\Http\Requests\Reload;

use Fintech\Core\Rules\MobileNumber;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInteracTransferRequest extends FormRequest
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
            'user_id' => ['nullable', 'integer', 'min:1'],
            'source_country_id' => ['required', 'integer', 'min:1'],
            'destination_country_id' => ['required', 'integer', 'min:1', 'same:source_country_id'],
            'service_id' => ['required', 'integer', 'min:1'],
            'ordered_at' => ['required', 'date', 'date_format:Y-m-d H:i:s', 'before_or_equal:'.date('Y-m-d H:i:s', strtotime('+3 seconds'))],
            'amount' => ['required', 'numeric'],
            'currency' => ['required', 'string', 'size:3'],
            'order_data' => ['nullable', 'array'],
            'order_data.request_from' => ['string', 'required'],
            'order_data.interac_data' => ['array', 'required'],
            'order_data.interac_data.narration' => ['string', 'nullable', 'max:255'],
            'order_data.interac_data.email' => ['string', 'required', 'email:rfc,dns'],
            'order_data.interac_data.first_name' => ['string', 'required', 'min:2', 'max:255'],
            'order_data.interac_data.last_name' => ['string', 'required', 'min:2', 'max:255'],
            'order_data.interac_data.phone' => ['string', 'required', new MobileNumber],
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
