<?php

namespace Fintech\RestApi\Http\Requests\Business;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceVendorRequest extends FormRequest
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
        $service_vendor_id = (int) collect(request()->segments())->last(); //id of the resource
        $uniqueRule = 'unique:service_vendors,service_vendor_slug,'.$service_vendor_id.',id,deleted_at,NULL';

        return [
            'service_vendor_name' => ['string', 'required', 'max:255'],
            'service_vendor_slug' => ['string', 'required', $uniqueRule],
            'logo_png' => ['string', 'nullable'],
            'logo_svg' => ['string', 'nullable'],
            'service_vendor_data' => ['nullable', 'array'],
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
