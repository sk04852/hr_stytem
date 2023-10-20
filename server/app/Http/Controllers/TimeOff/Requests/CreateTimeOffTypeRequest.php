<?php

namespace App\Http\Controllers\TimeOff\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class CreateTimeOffTypeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'policy_id'=> 'nullable|exists:policies,id',
            'allocation_method'=> [
                'nullable',
                Rule::in(['Accrual', 'Unlimited']),
            ],
            'allocation_method'=> [
                'nullable',
                Rule::in(['Jan-Dec', 'Jun-May', 'Custom']),
            ]
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
