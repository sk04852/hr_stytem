<?php

namespace App\Http\Controllers\AccountingAccountTypes\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAccountTypeRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [

            "account_type" => ['required','unique:account_types,account_type'],
            "detail" => ["nullable"],
        ];
    }

}
