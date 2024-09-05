<?php

namespace Fintech\RestApi\Http\Requests\Business;

use Fintech\Business\Facades\Business;
use Fintech\Core\Rules\ServiceTypeParent;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
        $service_id = (int)collect(request()->segments())->last(); //id of the resource
        $uniqueRule = 'unique:services,service_slug,' . $service_id . ',id,service_type_id,' . $this->input('service_type_id') . ',service_vendor_id,' . $this->input('service_vendor_id') . ',deleted_at,NULL';

        $rules = [
            'service_type_id' => ['integer', 'required', new ServiceTypeParent('no')],
            'service_vendor_id' => ['integer', 'required'],
            'service_name' => ['string', 'required', 'max:255'],
            'service_slug' => ['string', 'required', 'max:255', $uniqueRule],
            'service_notification' => ['string', 'nullable'],
            'service_delay' => ['string', 'nullable'],
            'service_stat_policy' => ['string', 'nullable'],
            'service_serial' => ['integer', 'required'],
            'logo_svg' => ['string', 'nullable'],
            'logo_png' => ['string', 'nullable'],
            'service_data' => ['array', 'nullable'],
            'enabled' => ['boolean', 'nullable', 'min:1'],
        ];

        Business::serviceSetting()->list([
            'paginate' => false,
            'service_setting_type' => 'service',
        ])->each(function ($serviceSetting) use (&$rules) {
            $validation = $serviceSetting->service_setting_rule ?? 'nullable';
            $rules["service_data.{$serviceSetting->service_setting_field_name}"] = explode('|', $validation);
        });

        return $rules;
    }

    /**
     * Get the validation attributes that apply to the request.
     */
    public function attributes(): array
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     */
    public function messages(): array
    {
        return [
            //
        ];
    }
}
