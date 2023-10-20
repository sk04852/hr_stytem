<?php

namespace App\Http\Controllers\PromoCodes\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class CreatePromoCodeRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            "promo_code" => "required|unique:promo_codes",
            "user_id" => "required|exists:users,id",
            "company_id" => "required|exists:companies,id",
            "brand_id" => "required|exists:brands,id",
            "status" => [Rule::in(
                [
                    'Active',
                    'Inactive'
                ]
            )],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
