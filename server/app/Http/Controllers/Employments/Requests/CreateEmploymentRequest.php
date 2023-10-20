<?php

namespace App\Http\Controllers\Employments\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEmploymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [

            'employee_id' => ['required','exists:employees,id'],
            'position_id' => ['nullable','exists:generic_designations,id'],
            'work_schedule_id' => ['nullable', 'exists:generic_work_schedules,id'], 
            'job_type_id' => ['nullable', 'exists:generic_job_types,id'], 
            'team_id' => ['nullable', 'exists:generic_teams,id'], 
            'location_id' => ['nullable', 'exists:generic_locations,id'], 
            'probation_start' => ['nullable','date_format:Y-m-d'],
            'probation_end' => ['nullable','date_format:Y-m-d'],
            'employment_start_date' => ['nullable', 'date_format:Y-m-d','after:probation_end'],
            'employment_end_date' => ['nullable', 'date_format:Y-m-d','after:employment_start_date'],
            
        ];
    }

}
