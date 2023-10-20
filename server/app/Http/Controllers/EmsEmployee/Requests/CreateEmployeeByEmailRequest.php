<?php

namespace App\Http\Controllers\EmsEmployee\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateEmployeeByEmailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [

            'email' => ['required','email', 'Unique:users'],
      
        ];
    }

}
