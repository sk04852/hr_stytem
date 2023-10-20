<?php

namespace App\Http\Controllers\Expenses\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenseCategoryRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required', 'exists:expense_categories,id'],
            'name' => ['required','string','max:150', Rule::unique('expense_categories')->where(function($query) {
                $query->where('created_by', '=', auth()->user()->id);
             })],
        ];
    }

}
