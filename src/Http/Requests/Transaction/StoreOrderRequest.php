<?php

namespace Fintech\RestApi\Http\Requests\Transaction;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'source_country_id' => ['required', 'integer', 'min:1'],
            'destination_country_id' => ['required', 'integer', 'min:1'],
            'parent_id' => ['nullable', 'integer', 'min:1'],
            'sender_receiver_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1'],
            'service_id' => ['required', 'integer', 'min:1'],
            'transaction_form_id' => ['required', 'integer', 'min:1'],
            'ordered_at' => ['required', 'datetime'],
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['required', 'string', 'min:3', 'max:3'],
            'order_number' => ['nullable', 'string'],
            'risk' => ['nullable', 'string'],
            'notes' => ['nullable', 'string', 'min:1'],
            'is_refunded' => ['required', 'boolean'],
            'order_data' => ['required', 'array'],
            'status' => ['required', 'string'],
        ];
    }

    protected function prepareForValidation()
    {
        $order_data = $this->input('order_data');
        $order_data['request_from'] = request()->platform()->value;
        $this->merge(['order_data' => $order_data]);
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
