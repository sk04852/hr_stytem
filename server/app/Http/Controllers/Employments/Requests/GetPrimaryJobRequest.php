<?php

namespace App\Http\Controllers\Employments\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetPrimaryJobRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'employee_id' => ['required','exists:employments,employee_id'],
        ];
    }
}
