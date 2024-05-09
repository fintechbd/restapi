<?php

namespace Fintech\RestApi\Http\Requests\MetaData;

use Fintech\RestApi\Traits\HasPaginateQuery;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexCountryRequest extends FormRequest
{
    use HasPaginateQuery;

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
            'search' => ['string', 'nullable', 'max:255'],
            'region_id' => ['integer', 'nullable', 'min:1'],
            'subregion_id' => ['integer', 'nullable', 'min:1'],
            'per_page' => ['integer', 'nullable', 'min:10', 'max:500'],
            'page' => ['integer', 'nullable', 'min:1'],
            'paginate' => ['boolean'],
            'language_enabled' => ['boolean', 'nullable'],
            'is_serving' => ['boolean', 'nullable'],
            'multi_currency_enabled' => ['boolean', 'nullable'],
            'sort' => ['string', 'nullable', 'min:2', 'max:255'],
            'dir' => ['string', 'min:3', 'max:4'],
            'trashed' => ['boolean', 'nullable'],
            'enabled' => ['boolean', 'nullable'],
        ];
    }

    protected function prepareForValidation()
    {
        $options = [];

        $languageEnabledInput = $this->input('language_enabled', '');
        $isServingInput = $this->input('is_serving', '');
        $multiCurrencyEnabledInput = $this->input('multi_currency_enabled', '');

        if ($languageEnabledInput != null && strlen($languageEnabledInput) != 0) {
            $options['language_enabled'] = $this->boolean('language_enabled', true);
        }

        if ($isServingInput != null && strlen($isServingInput) != 0) {
            $options['is_serving'] = $this->boolean('is_serving', true);
        }

        if ($multiCurrencyEnabledInput != null && strlen($multiCurrencyEnabledInput) != 0) {
            $options['multi_currency_enabled'] = $this->boolean('multi_currency_enabled', true);
        }

        $this->merge(array_merge($this->getPaginateOptions(), $options));
    }
}
