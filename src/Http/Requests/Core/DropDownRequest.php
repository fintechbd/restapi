<?php

namespace Fintech\RestApi\Http\Requests\Core;

use Fintech\Core\Supports\Utility;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DropDownRequest extends FormRequest
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
        return [
            'label' => ['string', 'nullable'],
            'attribute' => ['nullable', 'string'],
            'paginate' => ['boolean', 'nullable'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $options = [];

        foreach ($this->all() as $query => $value) {

            if ($value == 'true' || $value == 'false') {
                $options[$query] = Utility::typeCast($value, 'bool');
            } elseif (filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)) {
                $options[$query] = Utility::typeCast($value, 'float');
            } elseif (filter_var($value, FILTER_SANITIZE_NUMBER_INT)) {
                $options[$query] = Utility::typeCast($value, 'integer');
            }

        }

        $this->merge($options);
    }
}
