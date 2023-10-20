<?php

namespace App\Http\Controllers\MonetaryTransactions\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateMonetaryTransactionRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [

            "trading_account_id" => "required|exists:trading_accounts,id",
            "account_name" => "required",
            "transaction_type_id" => "required|exists:transaction_types,id",
            "amount" => "required",
            "assigned_to" => "required|exists:users,id",
            "transaction_id" => "nullable|unique:monetary_transactions",

            "payment_method" => "nullable|exists:field_options,id",
            "gateway_instance" => "nullable|exists:field_options,id",
            "gateway_name" => "nullable|exists:field_options,id",
            "channel" => "nullable|exists:field_options,id",
            "coupon" => "nullable",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
