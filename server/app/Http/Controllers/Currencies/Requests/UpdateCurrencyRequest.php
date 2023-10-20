<?php

namespace App\Http\Controllers\Currencies\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCurrencyRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "currency_id" => "required|exists:currencies,id",
            "company_id" => "required|exists:companies,id",
            "name" =>    Rule::requiredIf($this->currency_code == NULL),
            "currency_code" => ["min:3|max:3",  
                                Rule::unique('currencies')->ignore($this->user)->where('company_id', $this->company_id),
                                Rule::requiredIf($this->name == NULL)]
        ];
    }
}
