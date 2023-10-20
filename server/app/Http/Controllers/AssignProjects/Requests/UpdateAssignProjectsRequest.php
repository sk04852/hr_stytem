<?php

namespace App\Http\Controllers\AssignProjects\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateAssignProjectsRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'id' => ['required', 'exists:assign_projects,id'],
            'project_id' => ['required', 'exists:projects,id'],
            'team_id' => ['required' ,'exists:generic_teams,id'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
