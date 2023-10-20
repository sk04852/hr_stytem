<?php

namespace App\Http\Controllers\Expenses\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateExpenseRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [

            'merchant' => ['required'],
            'date' => ['required', 'date_format:Y-m-d'],
            'amount' => ['required', 'numeric'],
            'currancy' => ['nullable'],
            'category_id' => ['required', 'exists:expense_categories,id'],
            'reporter_id' => ['required', 'exists:users,id'],
            'description' => ['nullable'],
            
        ];
    }

}
