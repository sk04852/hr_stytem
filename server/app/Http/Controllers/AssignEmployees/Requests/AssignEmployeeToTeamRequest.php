<?php

namespace App\Http\Controllers\AssignEmployees\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AssignEmployeeToTeamRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'employee_id' => ['required', 'array', 'exists:employees,id'],
            'team_id' => ['required', 'array', 'exists:generic_teams,id'],
            'status' => ['required', 'array'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
