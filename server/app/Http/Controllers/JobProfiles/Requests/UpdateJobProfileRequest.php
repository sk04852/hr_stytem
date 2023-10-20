<?php

namespace App\Http\Controllers\JobProfiles\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|exists:job-pr,id',
            'jobpr-ID' => 'required',
            'Job-ID' => 'nullable|exists:job,id',
            'workers' => 'nullable',
            'companypr-ID' => 'nullable|exists:|exists:candidatecv,id,id',
            'userpr-ID' => 'nullable|exists:user-pr,id',
            'Deadline' => 'nullable',
            'Site' => 'nullable',
            'salary' => 'nullable',
            'Bonous' => 'nullable',
            'Optional' => 'nullable',
            'Date-created' => 'nullable',
        ];
    }
}
