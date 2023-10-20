<?php

namespace App\Http\Controllers\Currencies\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class CreateCurrencyRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            "company_id" => "required|exists:companies,id",
            "name" =>    Rule::requiredIf($this->currency_code == NULL),
            "currency_code" => ["min:3|max:3",  
                                Rule::unique('currencies')->where('company_id', $this->company_id),
                                Rule::requiredIf($this->name == NULL)]
        ];
    }
}
