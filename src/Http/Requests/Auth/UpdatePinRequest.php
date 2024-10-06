<?php

namespace Fintech\RestApi\Http\Requests\Auth;

use Fintech\Core\Rules\CurrentPin;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePinRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current' => ['required', 'string', 'min:6', 'max:255', new CurrentPin],
            'pin' => ['required', 'string', 'min:6', 'max:255', 'confirmed', 'different:current'],
        ];
    }
}