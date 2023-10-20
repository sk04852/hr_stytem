<?php

namespace App\Http\Controllers\Acl\Profiles\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required','integer','exists:acl_profiles,id'],
            'name' => ['required','string','max:255', Rule::unique('acl_profiles')->ignore($this->id)],
            'attributes'=> ['required', 'array',
            function($attribute, $value, $fail) {
                vaidateACLProfileAttributtes($attribute, $value, $fail);
            }
        ]
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
