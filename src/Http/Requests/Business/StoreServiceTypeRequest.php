<?php

namespace Fintech\RestApi\Http\Requests\Business;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreServiceTypeRequest extends FormRequest
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
        /** @phpstan-ignore-next-line */
        $service_type_id = (int)collect(request()->segments())->last(); //id of the resource
        $uniqueRule = 'unique:service_types,service_type_slug,' . $service_type_id . ',id,deleted_at,NULL';

        return [
            'service_type_parent_id' => ['integer', 'nullable'],
            'service_type_name' => ['string', 'required', 'max:255'],
            'service_type_slug' => ['string', 'required', 'max:255', $uniqueRule],
            'service_type_is_parent' => ['string', 'required'],
            'service_type_step' => ['integer', 'nullable'],
            'service_type_data' => ['array', 'nullable'],
            'logo_svg' => ['string', 'nullable'],
            'logo_png' => ['string', 'nullable'],
            'enabled' => ['boolean', 'nullable', 'min:1'],
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
