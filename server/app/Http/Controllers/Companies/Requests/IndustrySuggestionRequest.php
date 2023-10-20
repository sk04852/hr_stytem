<?php

namespace App\Http\Controllers\Companies\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndustrySuggestionRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'industry_name' => ['required'],
        ];
    }
}
