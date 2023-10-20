<?php

namespace App\Http\Controllers\Employments\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateEmploymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'employee_id' => ['required','exists:employments,employee_id'],
            'work_schedule_id' => ['required', 'exists:generic_work_schedules,id'], 
            'position_id' => ['required', 'exists:generic_job_types,id'], 
            'team_id' => ['nullable', 'exists:generic_teams,id'], 
            'location_id' => ['nullable', 'exists:generic_locations,id'], 
            'probation_start' => ['nullable','date_format:Y-m-d'],
            'probation_end' => ['nullable','date_format:Y-m-d'],
            'employment_start_date' => ['nullable', 'date_format:Y-m-d','after:probation_end'],
            'employment_end_date' => ['nullable', 'date_format:Y-m-d','after:employment_start_date'],
            'new_position_id' => ['nullable','exists:generic_designations,id'],
        ];
    }

}
