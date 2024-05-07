<?php

namespace Fintech\RestApi\Http\Requests\Transaction;

use Fintech\Transaction\Models\ChartEntry;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreChartEntryRequest extends FormRequest
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
        $uniqueRule = 'unique:'.config('fintech.transaction.chart_entry_model', ChartEntry::class).',code';

        return [
            'chart_type_id' => ['required', 'integer', 'min:1'],
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'code' => ['required', 'string', 'min:3', 'max:255', $uniqueRule],
            'chart_entry_data' => ['nullable', 'array'],
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
