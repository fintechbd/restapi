<?php

namespace Fintech\RestApi\Http\Requests\Auth;

use Fintech\Core\Enums\Auth\UserStatus;
use Fintech\Core\Enums\MetaData\CatalogType;
use Fintech\Core\Rules\Base64File;
use Fintech\Core\Rules\MobileNumber;
use Fintech\MetaData\Facades\MetaData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Symfony\Component\Mime\Email;

class UserProfileUpdateRequest extends FormRequest
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
        $userRules = [
            'name' => ['bail', 'nullable', 'string', 'max:255'],
            'mobile' => ['bail', 'nullable', 'string', 'max:255', 'min:10', new MobileNumber, Rule::unique('users', 'mobile')
                ->ignore($this->user('sanctum')->getKey())],
            'email' => ['bail', 'nullable', 'string', 'max:255', 'min:5', 'email:dns,rfc',
                Rule::unique('users', 'email')
                    ->ignore($this->user('sanctum')->getKey())
            ],
            'login_id' => ['bail', 'nullable', 'string', 'max:255', Rule::unique('users', 'login_id')
                ->ignore($this->user('sanctum')->getKey())],
            'status' => ['bail', 'nullable', 'string', 'max:255', Rule::in(UserStatus::values())],
            'language' => ['bail', 'nullable', 'string', 'max:255', Rule::in(MetaData::language()->list(['enabled' => true])->pluck('language.code')->unique()->toArray())],
            'currency' => ['bail', 'nullable', 'string', 'max:255', Rule::in(MetaData::currency()->list(['enabled' => true])->pluck('currency')->unique()->toArray())],
            'fcm_token' => ['bail', 'nullable', 'string', 'max:255'],
            'photo' => ['bail', 'nullable', 'string', new Base64File()],
        ];


        $profileRules = [
            'id_doc_type_id' => ['bail', 'nullable', 'integer'],
            'id_type' => ['bail', 'nullable', 'string', 'max:255'],
            'id_no' => ['bail', 'nullable', 'string', 'max:255'],
            'id_issue_country' => ['bail', 'nullable', 'string', 'max:255'],
            'id_expired_at' => ['bail', 'nullable', 'date', 'date_format:Y-m-d'],
            'id_issue_at' => ['bail', 'nullable', 'date', 'date_format:Y-m-d'],
            'date_of_birth' => ['bail', 'nullable', 'date', 'date_format:Y-m-d'],
            'permanent_address' => ['bail', 'nullable', 'string', 'max:255'],
            'permanent_city_id' => ['bail', 'nullable', 'integer'],
            'permanent_state_id' => ['bail', 'nullable', 'integer'],
            'permanent_country_id' => ['bail', 'nullable', 'integer'],
            'permanent_post_code' => ['bail', 'nullable', 'string', 'max:255'],
            'present_address' => ['bail', 'nullable', 'string', 'max:255'],
            'present_city_id' => ['bail', 'nullable', 'integer'],
            'present_state_id' => ['bail', 'nullable', 'integer'],
            'present_country_id' => ['bail', 'nullable', 'integer'],
            'present_post_code' => ['bail', 'nullable', 'string', 'max:255'],
            'user_profile_data' => ['bail', 'nullable', 'array'],
            'user_profile_data.father_name' => ['bail', 'nullable', 'string', 'max:255'],
            'user_profile_data.mother_name' => ['bail', 'nullable', 'string', 'max:255'],
            'user_profile_data.nationality' => ['bail', 'nullable', 'string', 'max:255'],
            'user_profile_data.gender' => [
                'bail', 'nullable', 'string', 'max:255',
                Rule::exists('catalogs', 'code')
                    ->where(fn($query) => $query->where([
                        'enabled' => true,
                        'type' => CatalogType::Gender->value
                    ]))
                ,],
            'user_profile_data.occupation' => [
                'bail', 'nullable', 'string', 'max:255',
                Rule::exists('catalogs', 'code')
                    ->where(fn($query) => $query->where([
                        'enabled' => true,
                        'type' => CatalogType::Occupation->value
                    ]))
            ],
            'user_profile_data.marital_status' => [
                'bail', 'nullable', 'string', 'max:255',
                Rule::exists('catalogs', 'code')
                    ->where(fn($query) => $query->where([
                        'enabled' => true,
                        'type' => CatalogType::MaritalStatus->value
                    ]))
            ],
            'user_profile_data.source_of_income' => [
                'bail', 'nullable', 'string', 'max:255',
                Rule::exists('catalogs', 'code')
                    ->where(fn($query) => $query->where([
                        'enabled' => true,
                        'type' => CatalogType::FundSource->value
                    ]))
            ]
        ];

        return $userRules + $profileRules;
    }

    public function attributes()
    {
        return [
            'login_id' => 'Login ID',
            'fcm_token' => 'Push Notification Token',
            'id_doc_type_id' => 'Document Type ID',
            'id_type' => 'Document Type',
            'id_no' => 'Document Number',
            'id_issue_country' => 'Issue Country',
            'id_expired_at' => 'Expire Date',
            'id_issue_at' => 'Issue Date',
            'date_of_birth' => 'Birth Date',
            'permanent_address' => 'Permanent Street Address',
            'permanent_city_id' => 'Permanent City',
            'permanent_state_id' => 'Permanent State',
            'permanent_country_id' => 'Permanent Country',
            'permanent_post_code' => 'Permanent Post Code',
            'present_address' => 'Present Street Address',
            'present_city_id' => 'Present City',
            'present_state_id' => 'Present State',
            'present_country_id' => 'Present Country',
            'present_post_code' => 'Permanent Post Code',
            'user_profile_data' => 'Profile Data',
            'user_profile_data.father_name' => 'Father Name',
            'user_profile_data.mother_name' => 'Mother Name',
            'user_profile_data.nationality' => 'Nationality',
            'user_profile_data.gender' => 'Gender',
            'user_profile_data.occupation' => 'Occupation',
            'user_profile_data.marital_status' => 'Marital Status',
            'user_profile_data.source_of_income' => 'Source Of Income',
        ];
    }
}
