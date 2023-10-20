<?php

namespace App\Http\Controllers\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeRoleRequest extends FormRequest
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
            'user_pr_id' => 'required|exists:user_pr,id',
            'role_id' => 'required|exists:roles,id'
        ];
    }
}
