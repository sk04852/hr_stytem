<?php

namespace App\Http\Controllers\CandidateCV\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdditionalCourseRequest extends FormRequest
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
            'id' => ['required', Rule::exists('candidatecv_additional_courses', 'id')->whereNull('deleted_at')],
            'candidatecv_id' => ['required', Rule::exists('candidatecv', 'id')->whereNull('deleted_at')],
            'title' => ['nullable','max:255'],
            'description' => ['required','max:255'],
            'starting_year' => ['required', 'digits:4', 'integer', 'min:1900', 'max:2050'],
            'starting_month' => ['nullable', 'digits_between:1,2', 'integer', 'between:1,12'],
            'starting_day' => ['nullable', 'digits_between:1,2', 'between:01,31'],
            'ending_year' =>  ['nullable', 'digits:4', 'integer', 'min:1900', 'max:2050'],
            'ending_month' =>  ['nullable', 'digits_between:1,2', 'integer', 'between:1,12'],
            'ending_day' => ['nullable', 'digits_between:1,2', 'between:01,31'],
            'total_hours' => ['nullable', 'numeric']
        ];
    }
}
