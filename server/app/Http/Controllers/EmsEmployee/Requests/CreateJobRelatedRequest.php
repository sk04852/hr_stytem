<?php

namespace App\Http\Controllers\EmsEmployee\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateJobRelatedRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:employees,id',
            'designation_id' => ['nullable', 'string', 'exists:generic_designations,id'],
            'team_id' => ['nullable', 'string', 'exists:generic_teams,id'],
            'location_id' => ['nullable', 'string', 'exists:generic_locations,id'],
            'job_type_id' => ['nullable', 'string', 'exists:generic_job_types,id'],
            'work_schedule_id' => ['nullable', 'string', 'exists:generic_work_schedules,id'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
