<?php

namespace App\Http\Controllers\People\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class PersonCreateRequest extends FormRequest
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
            'full_name' => 'required|max:20',
            'first_name' => 'nullable|max:20',
            'last_name' => 'nullable|max:20',
            'phone_number' => 'required|digits:11',
            'gender' => 'nullable|in:Male,Female,Unspecified',
            'user_type' => 'required|in:tutor,student',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|min:6,max:20|confirmed',
            'password_confirmation' => 'required|min:6,max:20',
            'is_terms_conditions_accepted' => 'required|boolean',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
