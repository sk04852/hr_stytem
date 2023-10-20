<?php

namespace App\Http\Controllers\Jobs\Requests;

use App\Http\Controllers\Jobs\Models\JobPr;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateJobRequest extends FormRequest
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
            'company_pr_id' => ['required', Rule::exists('companies_pr', 'id')->whereNull('deleted_at')],
            'status' => 'required|integer',
            'deadline' => 'required|date_format:Y-m-d',
            'required_candidates' => 'required|integer',
            'creator' => 'nullable|max:255',
            'contact_name' => 'required|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_number' => 'required|max:255',
            'training' => 'required|boolean',
            'observation' => 'required|boolean',
            'desired_language_comment' => 'nullable|max:16777215',
            'salary' => 'required|max:50',
            'salary_type' => 'required|max:50',
            'salary_amount_1' => 'required|integer',
            'salary_amount_2' => 'nullable|integer',
            'job_type' => 'required|max:50',
            'duration_type' => ['required','integer', Rule::in([JobPr::JOB_DURATION_PERMANENT, JobPr::JOB_DURATION_TEMPORARY])],
            'employment_type' => ['required','integer', Rule::in([JobPr::EMPLOYMENT_TYPE_FULL_TIME, JobPr::EMPLOYMENT_TYPE_PART_TIME, JobPr::EMPLOYMENT_TYPE_SUBSTITUE_TIME])],
            'job_type_comment' => 'nullable|max:16777215',
            'transport' => 'required|max:50',
            'transport_comment' => 'nullable|max:16777215',
            'working_hours' => 'required|max:50',
            'working_hours_comment' => 'nullable|max:16777215',
            'clothes' => 'required|max:50',
            'clothes_comment' => 'nullable|max:16777215',
            'offer_name' => 'required|max:255',
            'shifts' => 'required|max:50',
            'title' => 'nullable|max:255',
            'benefits' => 'nullable|max:16777215',
            'location' => 'nullable|max:255',
            'department' => 'nullable|max:255',
            'description' => 'nullable|max:16777215',
            'requirements' => 'nullable|max:16777215',
            'comments' => 'nullable|max:16777215',
            'additional_information' => 'nullable|max:16777215',
            'recess' => 'nullable|max:255',
            'work_language' => 'required|array',
            'work_language.*' => 'exists:languages,id',
            'desired_language' => 'nullable|array',
            'desired_language.*' => 'nullable|exists:languages,id',
            'shifts_data' => 'nullable|array',
            'shifts_data.*.start_time' => 'date_format:H:i',
            'shifts_data.*.end_time' => 'date_format:H:i|after:shifts_data.*.start_time',
            'files' => 'nullable|array',
            'files.*' => 'file',
            'video' => 'nullable|array',
            'video.*.type' => 'nullable|integer',
            'video.*.file' => 'nullable|file',
            'video.*.link' => 'nullable|max:255'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'company_pr_id.exists' => 'Company Dose not exists'
        ];
    }
}
