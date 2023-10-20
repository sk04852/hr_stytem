<?php

namespace App\Http\Controllers\Groups\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\validation\Rule;

class UpdateGroupRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            
            'id' => ['required','exists:groups,id'],
            'group_name' => ['required','string',Rule::unique('groups')->ignore($this->id)],
            'is_default' => ['nullable',Rule::in([
                'yes'
            ])] 
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
