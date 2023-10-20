<?php

namespace App\Http\Controllers\Acl\Permissions\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreatePermissionRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required','string','max:255','unique:acl_permissions'],
            'module_id'=> ['required','integer','exists:modules,id'],
        ];
    }

    public function messages(){
        return [
            'name.required' => 'The permission field is required',
            'name.unique' => 'The permission has already been taken',
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
