<?php

namespace App\Http\Controllers\TradingAccounts\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;


class TradingAccountUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'account_id' => 'required|numeric|exists:accounts,id',
            'platform' => ['nullable', Rule::in(['MT4', 'MT5'])],
            'currency' => ['required', Rule::in(['USD', 'EUR', 'GBP'])],
            'tp_account_id' => ['required'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
