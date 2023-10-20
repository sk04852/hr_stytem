<?php

namespace App\Http\Controllers\TransactionTypes\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransactionTypeRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [

            "id" => "required|exists:transaction_types,id",
            "name" => ['required', Rule::unique('transaction_types')->ignore($this->id)],
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
