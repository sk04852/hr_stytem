<?php

namespace App\Http\Controllers\TransactionTypes\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class CreateTransactionTypeRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [

            "name" => "required|unique:transaction_types,name",
            "type" => ['required', 'exists:field_options,id'],
            "action" => ['required', Rule::in(
                ['Add', 'Subtract']
            )],
            "function" => ['required', Rule::in(
                ['Deposit', 'Bonus']
            )],
            "comments" => "nullable",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
