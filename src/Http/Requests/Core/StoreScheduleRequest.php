<?php

namespace Fintech\RestApi\Http\Requests\Core;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'unique:schedules,name'],
            'description' => ['nullable', 'string'],
            'command' => ['required', 'string', 'unique:schedules,command'],
            'parameters' => ['nullable', 'array'],
            'timezone' => ['nullable', 'string'],
            'interval' => ['required', 'string'],
            'priority' => ['nullable', 'integer'],
            'enabled' => ['nullable', 'boolean'],
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
