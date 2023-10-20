<?php

namespace App\Http\Controllers\CandidateCV\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAdditionalCoursesRequest extends FormRequest
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
            'candidatecv_id' => ['required', Rule::exists('candidatecv', 'id')->whereNull('deleted_at')],
            'courses' => ['required', 'array'],
            'courses.*.title' => ['nullable', 'max:255'],
            'courses.*.description' => ['nullable', 'max:255'],
            'courses.*.starting_year' => ['required', 'digits:4', 'integer', 'min:1900', 'max:2050'],
            'courses.*.starting_month' => ['nullable', 'digits_between:1,2', 'integer', 'between:1,12'],
            'courses.*.starting_day' => ['nullable', 'digits_between:1,2', 'between:01,31'],
            'courses.*.ending_year' =>  ['nullable', 'digits:4', 'integer', 'min:1900', 'max:2050'],
            'courses.*.ending_month' =>  ['nullable', 'digits_between:1,2', 'integer', 'between:1,12'],
            'courses.*.ending_day' => ['nullable', 'digits_between:1,2', 'between:01,31'],
            'courses.*.total_hours' => ['nullable', 'numeric']
        ];
    }
}
