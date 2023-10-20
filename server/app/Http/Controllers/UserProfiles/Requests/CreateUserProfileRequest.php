<?php

namespace App\Http\Controllers\UserProfiles\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserProfileRequest extends FormRequest
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
            'userpr_ID' => 'required',
            'user_ID' => 'nullable|exists:users,id',
            'photo' => 'nullable',
            'int_code' => 'nullable',
            'phone' => 'nullable',
            'email' => 'nullable',
            'password' => 'nullable',
            'timezone' => 'nullable',
            'permission_ID' => 'nullable|exists:permissions,id',
        ];
    }
}
