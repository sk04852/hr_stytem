<?php

namespace App\Http\Controllers\Companies\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanySuggestionRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'company_name' => ['required'],
        ];
    }
}
