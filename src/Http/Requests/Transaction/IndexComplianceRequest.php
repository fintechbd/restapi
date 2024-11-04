<?php

namespace Fintech\RestApi\Http\Requests\Transaction;

use Fintech\Core\Enums\Auth\RiskProfile;
use Fintech\Core\Enums\Transaction\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexComplianceRequest extends FormRequest
{
    use \Fintech\RestApi\Traits\HasPaginateQuery;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['string', 'nullable', 'max:255'],
            'per_page' => ['integer', 'nullable', 'min:10', 'max:500'],
            'page' => ['integer', 'nullable', 'min:1'],
            'order_number' => ['string', 'nullable'],
            'user_id' => ['integer', 'nullable', 'min:1'],
            'source_country_id' => ['integer', 'nullable', 'min:1'],
            'destination_country_id' => ['string', 'nullable'],
            'code' => ['string', 'nullable'],
            'priority' => ['string', 'nullable', Rule::in(RiskProfile::values())],
            'risk' => ['string', 'nullable', Rule::in(RiskProfile::values())],
            'order_status' => ['string', 'nullable', Rule::in(OrderStatus::values())],
            'paginate' => ['boolean'],
            'sort' => ['string', 'nullable', 'min:2', 'max:255'],
            'dir' => ['string', 'min:3', 'max:4'],
            'trashed' => ['boolean', 'nullable'],
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
