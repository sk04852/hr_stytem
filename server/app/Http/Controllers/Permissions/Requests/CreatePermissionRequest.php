<?php

namespace App\Http\Controllers\Permissions\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePermissionRequest extends FormRequest
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
            'Role-ID' => 'required|max:20',
            'NAME' => ['nullable', 'string'],
            'Permission_id' => ['nullable', 'exists:permissions,id'],
        ];
    }
}
