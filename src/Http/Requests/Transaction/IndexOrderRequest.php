<?php

namespace Fintech\RestApi\Http\Requests\Transaction;

use Fintech\Core\Traits\HasPaginateQuery;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexOrderRequest extends FormRequest
{
    use HasPaginateQuery;

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
            'search' => ['string', 'nullable', 'max:255'],
            'per_page' => ['integer', 'nullable', 'min:10', 'max:500'],
            'page' => ['integer', 'nullable', 'min:1'],
            'paginate' => ['boolean'],
            'sort' => ['string', 'nullable', 'min:2', 'max:255'],
            'dir' => ['string', 'min:3', 'max:4'],
            'trashed' => ['boolean', 'nullable'],
            'user_id_not_in' => ['array', 'nullable'],
            'service_id_in' => ['array', 'nullable'],
            'transaction_form_id_in' => ['array', 'nullable'],
            'transaction_form_id_not_in' => ['array', 'nullable'],
            'user_id' => ['integer', 'nullable'],
            'order_id' => ['integer', 'nullable'],
            'source_country_id' => ['integer', 'nullable'],
            'sender_receiver_id' => ['integer', 'nullable'],
            'service_type_slug' => ['string', 'nullable'],
            'currency' => ['string', 'nullable'],
            'transaction_form_code' => ['string', 'nullable'],
            'service_id' => ['integer', 'nullable'],
            'transaction_form_id' => ['integer', 'nullable'],
            'order_start_date' => ['date', 'nullable'],
            'order_end_date' => ['date', 'nullable'],
            'status' => ['string', 'nullable'],
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
