<?php

namespace App\Http\Controllers\TimeOff\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateEmployeeTimeOffRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'employee_id' => 'nullable|exists:employees,id',
            'timeoff_type_id' => ['required', 'exists:timeoff_types,id'],
            'from_date' => ['required', 'date'],
            'time_off_for_from' => ['nullable', 'string'],
            'to_date' => ['required', 'date','after:from_date'],
            'time_off_for_to' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
            'absent_days' => ['nullable', 'string'],
            'attachment' => ['nullable', 'string'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
