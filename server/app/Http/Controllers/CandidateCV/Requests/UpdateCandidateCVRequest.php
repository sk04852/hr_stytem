<?php

namespace App\Http\Controllers\CandidateCV\Requests;

use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCandidateCVRequest extends FormRequest
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
            'id' => 'required|exists:candidatecv,id',
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'gender' => ['required','integer', Rule::in([CandidateCV::GENDER_MALE, CandidateCV::GENDER_FEMALE])],
            'dob' => 'nullable|date_format:Y-m-d',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email',
            'location' => 'nullable|max:255',
            'job_type' => 'nullable|array',
            'job_type.*' => 'exists:job_types,id',
            'mother_language' => 'nullable|max:50',
            'description' => 'nullable|max:65535',
            'source' => 'nullable|max:100',
            'photo' => 'nullable|image',
            'consent' => 'nullable|boolean',
            'newsletter' => 'nullable|boolean',
            'personal_information' => 'nullable|max:65535',
            'personal_code' => 'nullable|max:255',
            'children_qty' => 'nullable|max:65535',
            'children_names' => 'nullable|max:500',
            'marital_status' => 'nullable|max:50',
            'desired_job' => 'nullable|max:255',
            'desired_salary' => 'nullable|max:16777215',
            'desired_job_time' => 'nullable|max:255',
            'desired_job_location' => 'nullable|max:255',
            'age' => 'nullable|max:65535',
            'keywords' => 'nullable|max:65535',
            // 'tag_ids' => 'array',
            // 'tag_ids.*' => 'exists:tags,id',
            'tags' => 'nullable|array',
            'tags.*' => 'max:255',
            'nationality_ids' => 'array',
            'nationality_ids.*' => 'exists:nationalities,id',
            'driving_licenses' => 'nullable|array',
            'recommendations' => 'nullable|array',
            'status' => 'required|in:0,1'
        ];
    }
}
