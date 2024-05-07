<?php

namespace Fintech\RestApi\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
        $rules = config('fintech.auth.register_rules', [
            //user
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'mobile' => ['required', 'string', 'min:10'],
            'email' => ['required', 'string', 'email:rfc,dns', 'min:2', 'max:255'],
            'pin' => ['string', 'min:4', 'max:16'],
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
            'id_no' => ['string', 'nullable'],
            'id_issue_country' => ['string', 'nullable'],
            'id_expired_at' => ['string', 'nullable'],
            'id_issue_at' => ['string', 'nullable'],
            'photo' => ['string', 'nullable'],
            'documents' => ['array', 'nullable', 'min:1'],
            'documents.*.type' => ['string', 'required'],
            'documents.*.back' => ['string', 'required_without:documents.*.front'],
            'documents.*.front' => ['string', 'required_without:documents.*.back'],
            'employer' => ['array', 'nullable'],
            'employer.company_name' => ['string', 'nullable'],
            'employer.company_address' => ['string', 'nullable'],
            'employer.company_registration_number' => ['string', 'nullable'],
            'proof_of_address' => ['array', 'nullable', 'min:1'],
            'proof_of_address.*.type' => ['string', 'required'],
            'proof_of_address.*.back' => ['string', 'required_without:proof_of_address.*.front'],
            'proof_of_address.*.front' => ['string', 'required_without:proof_of_address.*.back'],
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

        $rules[config('fintech.auth.auth_field', 'login_id')] = config('fintech.auth.auth_field_rules', ['required', 'string', 'min:6', 'max:255']);

        $rules[config('fintech.auth.password_field', 'password')] = ['nullable', ...config('fintech.auth.password_field_rules', ['string', Password::default()])];

        $rules['pin'][] = 'nullable';

        $rules['roles'] = ['array', 'required'];

        $rules['roles.*'] = ['integer', 'required'];

        return $rules;
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
