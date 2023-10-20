<?php

namespace App\Http\Controllers\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'id' => 'required|exists:users,id',
            'userpr_id' => 'required|exists:user_pr,id',
            'name' => 'max:50',
            'job_title' => 'max:50',
            'location' => 'max:50',
            'phone' => 'max:20',
            'email' => 'email|unique:user_pr,email,'. $this->userpr_id,
            'password' => 'string|min:6|confirmed',
            'timezone_id' => 'exists:timezones,id',
            'role_id' => 'integer'
        ];
    }
}
