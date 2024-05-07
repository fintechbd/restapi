<?php

namespace Fintech\RestApi\Http\Requests\Promo;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePromotionRequest extends FormRequest
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
            'present_country_id' => ['integer', 'nullable', 'min:1'],
            'permanent_country_id' => ['integer', 'nullable', 'min:1'],
            'name' => ['required', 'string', 'min:5'],
            'type' => ['required', 'string', Rule::in(array_keys(config('fintech.promo.promotion_types', [])))],
            'content' => ['nullable', 'string', 'min:1'],
            'enabled' => ['required', 'boolean'],
            'photo' => ['nullable', 'string'],
            'promotion_data' => ['required', 'array'],
            'promotion_data.link.*' => ['nullable', 'string', 'url'],
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
