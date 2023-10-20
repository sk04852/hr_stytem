<?php

namespace App\Http\Controllers\CandidateCV\Requests;

use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCandidateCVRequest extends FormRequest
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
            'nationalities' => 'nullable|array',
//            'nationalities.*' => 'nullable|array',
            'tags' => 'nullable|array',
            'tags.*' => 'max:255',
            'driving_licenses' => 'nullable|array',
            'driving_licenses.*.level' => 'max:191',
            'driving_licenses.*.issue_date' => 'date_format:Y-m-d',
            'driving_licenses.*.expiry_date' => 'date_format:Y-m-d',
            'recommendations' => 'nullable|array',
            'recommendations.*' => 'max:65535',
            'status' => 'required|in:0,1'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
//     public function messages()
//     {
//         return [
//             'first_name.required' => 'First Name is required',
//             'first_name.max' => 'First Name can not exceed from 50 character',
//             'last_name' => 'required|max:50',
//             // 'gender' => 'required|in:male,female',
//             'dob' => 'nullable|date_format:Y-m-d',
//             'phone' => 'nullable|max:20',
//             'email' => 'nullable|email',
//             'location' => 'nullable|max:255',
//             'job_type' => 'nullable|array',
//             'job_type.*' => 'exists:job_types,id',
//             'mother_language' => 'nullable|max:50',
//             'description' => 'nullable|max:65535',
//             'source' => 'nullable|max:100',
//             'photo' => 'nullable|image',
//             'consent' => 'nullable|boolean',
//             'newsletter' => 'nullable|boolean',
//             'action_id' => 'nullable|exists:actions,id',
//             'personal_information' => 'nullable|max:255',
//             'personal_code' => 'nullable|max:255',
//             'children_qty' => 'nullable|max:65535',
//             'children_names' => 'nullable|max:500',
//             'marital_status' => 'nullable|max:50',
//             'desired_job' => 'nullable|max:255',
//             'desired_salary' => 'nullable|max:16777215',
//             'desired_job_time' => 'nullable|max:255',
//             'desired_job_location' => 'nullable|max:255',
//             'age' => 'nullable|max:65535',
//             'keywords' => 'nullable|max:65535',
//             'nationalities' => 'nullable|array',
// //            'nationalities.*' => 'nullable|array',
//             'tags' => 'nullable|array',
//             'tags.*' => 'max:255',
//             'driving_licenses' => 'nullable|array',
//             'driving_licenses.*.level' => 'max:191',
//             'driving_licenses.*.issue_date' => 'date_format:Y-m-d',
//             'driving_licenses.*.expiry_date' => 'date_format:Y-m-d',
//             'recommendations' => 'nullable|array',
//             'recommendations.*' => 'max:65535',
//             'status' => 'required|in:0,1'
//         ];
//     }
}
