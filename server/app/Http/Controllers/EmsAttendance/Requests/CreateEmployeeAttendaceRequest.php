<?php

namespace App\Http\Controllers\EmsAttendance\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateEmployeeAttendaceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'employee_id'=> 'required|exists:employees,id',
            'date'=> 'required|date_format:Y-m-d',
            'start_time'=> 'required|date_format:H:i',
            'end_time'=> 'nullable|date_format:H:i',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
