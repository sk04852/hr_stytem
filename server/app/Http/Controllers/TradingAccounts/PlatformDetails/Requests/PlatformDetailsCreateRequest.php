<?php

namespace App\Http\Controllers\TradingAccounts\PlatformDetails\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class PlatformDetailsCreateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
                'trading_account_id' => 'required|numeric|exists:trading_accounts,id',
                'trading_account_id' => 'numeric',
                'balance' => 'numeric',
                'credit' => 'numeric',
                'equity' => 'numeric',
                'margin' => 'numeric',
                'margin_free' => 'numeric',
                'closed_pnl' => 'numeric',
                'open_pnl' => 'numeric'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
