<?php

namespace Fintech\RestApi\Http\Requests\Tab;

use Fintech\Business\Facades\Business;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PayBillRequest extends FormRequest
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
        $rules = [
            'user_id' => ['nullable', 'integer', 'min:1'],
            'service_id' => ['required', 'integer', 'min:1'],
            'source_country_id' => ['required', 'integer', 'min:1'],
            'destination_country_id' => ['required', 'integer', 'min:1'],
            'reverse' => ['required', 'boolean'],
            'reload' => ['nullable', 'boolean'],
            'pay_bill_data' => ['nullable', 'array'],
        ];

        Business::serviceField()->list([
            'service_id' => $this->input('service_id'),
            'paginate' => false,
            'enabled' => true,
        ])->each(function ($field) use (&$rules) {
            $rules['pay_bill_data.'.$field->name] = $field->validation;
        });

        return $rules;
    }
}
