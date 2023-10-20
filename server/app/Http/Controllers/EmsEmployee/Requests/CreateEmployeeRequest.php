<?php

namespace App\Http\Controllers\EmsEmployee\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'company_id'=> ['nullable', 'exists:companies,id'],
            'employee_number' => ['nullable', 'unique:employees', 'min:6', 'max:6'],
            'first_name' => ['required', 'max:20'],
            'last_name' => ['required', 'max:20'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6','max:20','confirmed'],
            'password_confirmation' => ['required', 'min:6','max:20'],
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
            'employment_start' => ['nullable', 'date_format:Y-m-d'],
            'birth_date' => ['nullable', 'date_format:Y-m-d'],
            'marital_status' => ['nullable', Rule::in(['Single', 'Married'])],
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
