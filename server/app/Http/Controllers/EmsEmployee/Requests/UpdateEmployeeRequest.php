<?php

namespace App\Http\Controllers\EmsEmployee\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required', 'exists:employees,id'],
            "email" => ["required","email", Rule::unique('users')->ignore($this->request->get('user_id'))],
            'password' => ['nullable', 'min:6','max:20','confirmed'],
            'address' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'code' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'zip' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'phone' => ['nullable', 'numeric', 'digits_between:6,15'],
            'mobile' => ['nullable', 'numeric', 'digits_between:6,15'],
            'facebook_links' => ['nullable', 'url'],
            'linkedin_links' => ['nullable', 'url'],
            'twitter_links' => ['nullable', 'url'],
            'profile_picture' => ['nullable', 'image' => 'file','size:3000', 'mimes:png,jpg'],
            'about_me' => ['nullable', 'string'],
            'employment_start' => ['nullable', 'date'],
            'salary' => ['nullable', 'numeric'],
            'birth_date' => ['nullable', 'date'],
            'marital_status' => ['nullable', 'string'],
            'nationality' => ['nullable', 'string'],
            'personal_phone' => ['nullable', 'numeric', 'digits_between:6,15'],
            'personal_email' => ['nullable', 'email'],
            'gender' => ['nullable', Rule::in(['Male', 'Female'])],
            'ec_full_name' => ['nullable', 'string'],
            'ec_phone' => ['nullable', 'numeric', 'digits_between:6,15'],
            'ec_email' => ['nullable', 'email'],
            'ec_relationship' => ['nullable', 'string'],
        ];
    }

}
