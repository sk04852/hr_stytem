<?php

namespace App\Http\Controllers\BankAccount\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateBankAccountRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'bank_id' => 'required',
            'account_number' => 'required|max:64',
            'iban' => 'required|max:64',
            'branch_code' => 'max:40',
            'swift_code' => 'required|max:40',
            'bic' => 'max:40',
            'currency' => 'required|max:3',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
