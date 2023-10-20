<?php

namespace App\Http\Controllers\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => ['required', Rule::exists('roles', 'id')],
            'name' => ['required','max:191', Rule::unique('roles', 'name')->ignore($this->id)],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'id.required' => __('validation.required'),
            'id.exists' => __('validation.exists'),
            'name.required' => __('validation.required'),
            'name.max' => __('validation.max'),
            'name.unique' => __('validation.unique'),
        ];
    }
}
