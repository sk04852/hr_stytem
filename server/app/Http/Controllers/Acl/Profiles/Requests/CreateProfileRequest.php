<?php

namespace App\Http\Controllers\Acl\Profiles\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class CreateProfileRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required','string','max:255','unique:acl_profiles'],
            'attributes'=> ['required', 'array',
                function($attribute, $value, $fail) {
                    vaidateACLProfileAttributtes($attribute, $value, $fail);
                }
            ]
        ];
    }

    public function messages(){
        return [
            'name.required' => 'The profile field is required',
            'name.unique' => 'The profile has already been taken',
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
