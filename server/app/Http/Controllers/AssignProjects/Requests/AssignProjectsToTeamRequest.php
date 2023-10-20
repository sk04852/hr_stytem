<?php

namespace App\Http\Controllers\AssignProjects\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignProjectsToTeamRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'project_id' => ['required','exists:projects,id'],
            'team_id' => ['required' ,'array','exists:generic_teams,id'],
        ];
    }

}
