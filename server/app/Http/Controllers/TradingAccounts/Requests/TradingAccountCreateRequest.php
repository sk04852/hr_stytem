<?php

namespace App\Http\Controllers\TradingAccounts\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class TradingAccountCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
                'account_id' => 'required|int|exists:accounts,id',
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
