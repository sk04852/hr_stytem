<?php

namespace App\Http\Controllers\TransactionTypes\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class UpdateMassTransactionTypesRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            "id" => "required|array|exists:transaction_types,id",
            "name" => "required|array|unique:transaction_types,name",
            "action" => ['required','array',Rule::in([
                'Credit',
                'Debit']
            )],
            "comments" => "nullable|array",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
