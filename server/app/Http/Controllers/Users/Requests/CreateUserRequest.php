<?php

namespace App\Http\Controllers\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
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
            'name' => 'required|max:50',
            'job_title' => 'nullable|max:50',
            'location' => 'nullable|max:50',
            'phone' => 'nullable|max:20',
            'email' => [
                'required',
                Rule::unique('user_pr', 'email')->whereNull('deleted_at'),
            ],
            'password' => 'required|string|min:6|confirmed',
            'timezone_id' => 'nullable|exists:timezones,id',
            'role_id' => 'nullable|integer'
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
            'name.required' => __('validation.required'),
            'name.max' => __('validation.max'),
            'job_title.required' => __('validation.required'),
            'job_title.max' => __('validation.max'),
            'location.required' => __('validation.required'),
            'location.max' => __('validation.max'),
            'phone.required' => __('validation.required'),
            'phone.max' => __('validation.max'),
            'email.required' => __('validation.required'),
            'email.unique' => __('validation.unique'),
            'password.required' => __('validation.required'),
            'password.string' => __('validation.string'),
            'password.min' => __('validation.min'),
            'password.confirmed' => __('validation.confirmed'),
        ];
    }
}
