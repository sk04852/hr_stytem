<?php

namespace App\Http\Controllers\AccountingAccountTypes\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccountTypeRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [

            "id" => ['required','exists:account_types,id'],
            "account_type" => ['required', Rule::unique('account_types')->where(function($query){
                    $query->where('id', '!=', $this->id);
            })],
            "detail" => ["nullable"],
        ];
    }
}
