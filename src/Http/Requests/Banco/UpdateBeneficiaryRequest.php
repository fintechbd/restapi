<?php

namespace Fintech\RestApi\Http\Requests\Banco;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property int $user_id
 * @property int $beneficiary_type_id
 */
class UpdateBeneficiaryRequest extends FormRequest
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
        $beneficiary_id = (int)collect(request()->segments())->last(); //id of the resource
//        $uniqueRule = 'unique:beneficiaries,beneficiary_mobile,' . $beneficiary_id . ',id,user_id,' . $this->input('user_id') . ',beneficiary_type_id,' . $this->input('beneficiary_type_id') . ',deleted_at,NULL';

        $uniqueRule = Rule::unique('beneficiaries', 'beneficiary_mobile')
            ->where(fn($query) => $query->where([
                'user_id' => $this->input('user_id'),
                'beneficiary_type_id' => $this->input('beneficiary_type_id'),
                'deleted_at' => null
            ]))
            ->ignore($beneficiary_id);

        return [
            'user_id' => ['required', 'integer'],
            'country_id' => ['required', 'integer'],
            'state_id' => ['required', 'integer'],
            'city_id' => ['required', 'integer'],
            'relation_id' => ['required', 'integer'],
            'beneficiary_type_id' => ['required', 'integer'],
            'beneficiary_name' => ['required', 'string', 'max:255'],
            'beneficiary_mobile' => ['required', 'string', 'min:8', 'max:16', 'regex:/[0-9]{9}/', $uniqueRule],
            'beneficiary_address' => ['nullable', 'string'],
            'photo' => ['nullable', 'string'],
            'beneficiary_data' => ['required', 'array'],
            'beneficiary_data.first_name' => ['nullable', 'string'],
            'beneficiary_data.last_name' => ['nullable', 'string'],
            'beneficiary_data.email' => ['nullable', 'string', 'email:rfs,dns'],
            'beneficiary_data.account_name' => ['nullable', 'string'],
            'beneficiary_data.cash_id' => ['nullable', 'integer'],
            'beneficiary_data.cash_account_number' => ['nullable', 'string'],
            'beneficiary_data.instant_bank_id' => ['nullable', 'integer'],
            'beneficiary_data.instant_bank_branch_id' => ['nullable', 'integer'],
            'beneficiary_data.instant_bank_account_number' => ['nullable', 'string'],
            'beneficiary_data.bank_id' => ['nullable', 'integer'],
            'beneficiary_data.bank_branch_id' => ['nullable', 'integer'],
            'beneficiary_data.bank_account_number' => ['nullable', 'string'],
            'beneficiary_data.id_type' => ['nullable', 'string'],
            'beneficiary_data.id_number' => ['nullable', 'string'],
            'beneficiary_data.id_issue_date' => ['nullable', 'string'],
            'beneficiary_data.id_expire_date' => ['nullable', 'string'],
            'beneficiary_data.id_image' => ['nullable', 'string'],
            'beneficiary_data.wallet_id' => ['nullable', 'integer'],
            'beneficiary_data.wallet_account_number' => ['nullable', 'string'],
            'beneficiary_data.beneficiary_type' => ['nullable', 'string'],
            'beneficiary_data.beneficiary_type_condition_name' => ['nullable', 'string'],
            'enabled' => ['boolean', 'nullable'],
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
