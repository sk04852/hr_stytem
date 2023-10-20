<?php

namespace App\Http\Controllers\UserProfiles\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
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
            'id' => 'required|exists:user-pr,id',
            'Userpr-ID' => 'required',
            'User-ID' => 'nullable|exists:users,id',
            'photo' => 'nullable',
            'int-code' => 'nullable',
            'phone' => 'nullable',
            'E-mail' => 'nullable',
            'password' => 'nullable',
            'Timezone' => 'nullable',
            'permission-ID' => 'nullable|exists:permissions,id',
        ];
    }
}
