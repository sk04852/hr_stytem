<?php

namespace App\Http\Controllers\AccountingTransactionAccounts\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [

            'id' => ['required', 'exists:accounts'],
            'account_type_id' => ['required','exists:account_types,id'],
            'account_number' => ['required', Rule::unique('accounts')->where(function($query) {
                $query->where('id', '!=', $this->id);
             })],
        ];
    }
}
