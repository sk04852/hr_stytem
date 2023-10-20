<?php

namespace App\Http\Controllers\Expenses\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenseRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'id' => ['required', Rule::unique('expenses')->ignore($this->id)],
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
