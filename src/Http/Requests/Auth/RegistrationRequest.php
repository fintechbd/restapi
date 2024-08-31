<?php

namespace Fintech\RestApi\Http\Requests\Auth;

use Fintech\Core\Facades\Core;
use Fintech\Core\Rules\MobileNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegistrationRequest extends FormRequest
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
        $rules = config('fintech.auth.register_rules', [
            //user
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'mobile' => ['required', 'string', 'min:10', 'max:15', new MobileNumber],
            'email' => ['required', 'string', 'email:rfc,dns', 'min:2', 'max:255'],
            'pin' => ['required', 'string', 'min:4', 'max:16'],
            'app_version' => ['nullable', 'string'],
            'fcm_token' => ['nullable', 'string'],
            'language' => ['nullable', 'string'],
            'currency' => ['nullable', 'string'],

            //profile
            'father_name' => ['string', 'nullable'],
            'mother_name' => ['string', 'nullable'],
            'gender' => ['string', 'nullable'],
            'marital_status' => ['string', 'nullable'],
            'occupation' => ['string', 'nullable'],
            'source_of_income' => ['string', 'nullable'],
            'id_type' => ['string', 'nullable'],
            'id_doc_type_id' => ['integer', 'required'],
            'id_no' => ['string', 'nullable'],
            'id_issue_country' => ['string', 'nullable'],
            'id_expired_at' => ['string', 'nullable'],
            'id_issue_at' => ['string', 'nullable'],
            'photo' => ['string', 'nullable'],
            'documents' => ['array', 'required', 'min:1'],
            'documents.*.type' => ['string', 'required'],
            'documents.*.back' => ['string', 'required_without:documents.*.front'],
            'documents.*.front' => ['string', 'required_without:documents.*.back'],
            'employer' => ['array', 'nullable'],
            'employer.company_name' => ['string', 'nullable'],
            'employer.company_address' => ['string', 'nullable'],
            'employer.company_registration_number' => ['string', 'nullable'],
            'proof_of_address' => ['array'],
            'proof_of_address.*.type' => ['string', 'required'],
            'proof_of_address.*.front' => ['string', 'required_without:proof_of_address.*.back'],
            'proof_of_address.*.back' => ['string', 'required_without:proof_of_address.*.front'],
            'date_of_birth' => ['date', 'nullable'],
            'permanent_address' => ['string', 'nullable'],
            'permanent_city_id' => ['integer', 'nullable'],
            'permanent_state_id' => ['integer', 'nullable'],
            'permanent_country_id' => ['integer', 'nullable'],
            'permanent_post_code' => ['string', 'nullable'],
            'present_address' => ['string', 'nullable'],
            'present_city_id' => ['integer', 'nullable'],
            'present_state_id' => ['integer', 'nullable'],
            'present_country_id' => ['integer', 'nullable'],
            'present_post_code' => ['string', 'nullable'],
            'nationality' => ['string', 'nullable'],
        ]);

        if (Core::packageExists('Ekyc')) {
            $rules['ekyc'] = ['required', 'array', 'min:3'];
            $rules['ekyc.reference_no'] = ['required', 'string', 'size:'.config('fintech.core.entry_number_length', 20)];
            $rules['ekyc.vendor'] = ['nullable', 'string', Rule::in(array_keys(config('fintech.ekyc.providers')))];
            $rules['ekyc.request'] = ['nullable', 'array'];
            $rules['ekyc.response'] = ['nullable', 'array'];
        }

        $login_id_rules = config('fintech.auth.auth_field_rules', ['required', 'string', 'min:6', 'max:255']);

        $login_id_rules[] = 'unique:users,login_id';

        $rules[config('fintech.auth.auth_field', 'login_id')] = $login_id_rules;

        $rules[config('fintech.auth.password_field', 'password')] = config('fintech.auth.password_field_rules', ['required', 'string', Password::default()]);

        $rules['pin'][] = 'required';

        return $rules;
    }
}
