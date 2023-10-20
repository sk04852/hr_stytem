<?php

namespace App\Http\Controllers\BankAccount\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class MassUpdateBankAccountRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'required|array',
            'bank_id' => 'required|exists:banks,id|array',
            'account_number' => 'required|array',
            'iban' => 'required|array',
            'branch_code' => 'array',
            'swift_code' => 'required|array',
            'bic' => 'array',
            'currency' => 'required|array',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
