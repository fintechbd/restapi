<?php

namespace Fintech\RestApi\Http\Requests\MetaData;

use Fintech\Core\Enums\MetaData\CatalogType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCatalogRequest extends FormRequest
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
        $type = $this->input('type');

        return [
            'type' => ['required', 'string', Rule::in(CatalogType::values())],
            'name' => [
                'required',
                'string',
                'min:5',
                'max:255',
                Rule::unique('catalogs', 'name')
                    ->where(fn ($query) => $query->where('type', $type)),
            ],
            'code' => [
                'required',
                'string',
                'min:5',
                'max:255',
                Rule::unique('catalogs', 'code')
                    ->where(fn ($query) => $query->where('type', $type)),
            ],
            'countries' => ['nullable', 'array'],
            'countries.*' => ['required', 'integer'],
            'vendor_code' => ['nullable', 'array'],
            'catalog_data' => ['nullable', 'array'],
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
