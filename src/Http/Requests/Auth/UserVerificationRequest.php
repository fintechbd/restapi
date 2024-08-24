<?php

namespace Fintech\RestApi\Http\Requests\Auth;

use Fintech\Core\Rules\MobileNumber;
use Illuminate\Foundation\Http\FormRequest;

class UserVerificationRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'mobile' => ['required_without_all:email,user', 'string', 'min:10', 'max:15', new MobileNumber],
            'email' => ['required_without_all:mobile,user', 'string', 'email:rfc,dns'],
            'login_id' => ['required_without_all:mobile,email', 'integer', 'min:1'],
        ];
    }
}
