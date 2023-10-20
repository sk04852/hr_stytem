<?php

namespace App\Http\Controllers\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignUserPermissionsRequest extends FormRequest
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
            'user_id' => 'required|exists:user_pr,id',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ];
    }
}
