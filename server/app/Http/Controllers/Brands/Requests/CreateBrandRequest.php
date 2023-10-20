<?php

namespace App\Http\Controllers\Brands\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class CreateBrandRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            "name" => "required|unique:brands,name",
            "primary_email" => "required|unique:brands,primary_email",
            "assigned_to" => "required|exists:users,id",
            "company_id" => "required|exists:companies,id",
            'status' => [Rule::in(
                [
                    'Enable',
                    'Disable'
                ]
            )],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
