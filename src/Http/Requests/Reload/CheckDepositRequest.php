<?php

namespace Fintech\RestApi\Http\Requests\Reload;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckDepositRequest extends FormRequest
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
        //        return [
        //            'password' => ['string', 'required_without:pin'],
        //            'pin' => ['string', 'required_without:password']
        //        ];

        return [

        ];
    }
}
