<?php

namespace App\Http\Controllers\Acl\Permissions\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdatePermissionRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required','integer','exists:acl_permissions,id'],
            'module_id'=> ['required','integer','exists:modules,id'],
            'name' => ['required','string','max:255', Rule::unique('acl_permissions')->ignore($this->id)],
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
