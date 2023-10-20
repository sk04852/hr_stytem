<?php

namespace App\Http\Controllers\Acl\Roles\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateRoleRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required','string','max:255','unique:acl_roles'],
        ];
    }

    public function messages(){
        return [
            'name.required' => 'The role field is required',
            'name.unique' => 'The role has already been taken',
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
