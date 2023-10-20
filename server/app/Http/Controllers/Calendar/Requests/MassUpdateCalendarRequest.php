<?php

namespace App\Http\Controllers\Calendar\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class MassUpdateCalendarRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "id"=>"required|array",
            "title"=> "array",
            "visibility"=> "array",
            "created_by"=> "required|exists:users,id|array",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
