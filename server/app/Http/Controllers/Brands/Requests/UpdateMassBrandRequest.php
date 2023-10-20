<?php

namespace App\Http\Controllers\Brands\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateMassBrandRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            "id" => "required|array|exists:brands,id",
            "name" => "required|unique:brands,name|array",
            "primary_email" => "required|unique:brands,primary_email|array",
            "assigned_to" => "required|exists:users,id|array"
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
