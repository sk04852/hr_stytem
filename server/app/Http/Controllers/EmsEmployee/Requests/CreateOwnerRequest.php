<?php

namespace App\Http\Controllers\EmsEmployee\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class CreateOwnerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'company_name' => ['required', 'string'],
            'employee_number' => 'nullable|min:6|max:6|unique:employees',
            'first_name' => 'required|max:20',
            'last_name' => 'required|max:20',
            'email' => ['required', 'Unique:users'],
            'password' => 'required|min:6,max:20|confirmed',
            'password_confirmation' => 'required|min:6,max:20',
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'code' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'zip' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'phone' => ['nullable', 'numeric', 'digits_between:8,15'],
            'mobile' => ['nullable', 'numeric', 'digits_between:8,15'],
            'facebook_links' => ['nullable', 'url'],
            'linkedin_links' => ['nullable', 'url'],
            'twitter_links' => ['nullable', 'url'],
            'about_me' => ['nullable', 'string'],
            'employment_start' => ['nullable', 'date'],
            'birth_date' => ['nullable', 'date'],
            'marital_status' => ['nullable', 'string'],
            'nationality' => ['nullable', 'string'],
            'personal_phone' => ['nullable', 'numeric', 'digits_between:10,15'],
            'personal_email' => ['nullable', 'email'],
            'gender' => ['nullable', Rule::in(['Male', 'Female'])],
            'ec_full_name' => ['nullable', 'string'],
            'ec_phone' => ['nullable', 'numeric', 'digits_between:10,15'],
            'ec_email' => ['nullable', 'email'],
            'ec_relationship' => ['nullable', 'string'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
