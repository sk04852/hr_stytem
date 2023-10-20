<?php

namespace App\Http\Controllers\AssignEmployees\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateAssignEmployeeRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'id' => ['required', 'exists:assign_employees,id'],
            'employee_id' => ['required' , 'exists:employees,id'],
            'team_id' => ['required', 'exists:generic_teams,id'],
            'status' => ['required'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
