<?php

namespace App\Http\Controllers\AccountingTransactionAccounts\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAccountRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [

            'account_type_id' => ['required','exists:account_types,id'],
            'account_number' => ['required', Rule::unique('accounts')->where(function($query) {
                $query->where('company_id', auth()->user()->company_id);
             })],
        ];
    }

}
