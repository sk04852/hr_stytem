<?php

namespace App\Http\Controllers\CandidateCV\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\Day;

class CreateEducationRequest extends FormRequest
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
            'education' => ['required', 'array'],
            'education.*.candidatecv_id' => ['required', Rule::exists('candidatecv', 'id')->whereNull('deleted_at')],
            // 'education.*.degree' => 'required|max:191',
            'education.*.level_id' => ['nullable', Rule::exists('education_levels', 'id')->whereNull('deleted_at')],
            'education.*.institute' => ['required', 'max:50'],
            'education.*.starting_year' => ['required', 'digits:4', 'integer', 'min:1900', 'max:2050'],
            'education.*.starting_month' => ['nullable', 'digits_between:1,2', 'integer', 'between:1,12'],
            'education.*.starting_day' => ['nullable', 'digits_between:1,2', 'between:01,31'],
            'education.*.ending_year' =>  ['exclude_if:education.*.still_studying,1', 'required', 'digits:4', 'integer', 'min:1900', 'max:2050'],
            'education.*.ending_month' =>  ['nullable', 'digits_between:1,2', 'integer', 'between:1,12'],
            'education.*.ending_day' => ['nullable', 'digits_between:1,2', 'between:01,31'],
            // 'education.*.starting_date' => 'required|date_format:Y-m-d',
            // 'education.*.ending_date' => 'nullable|date_format:Y-m-d',
            'education.*.speciality' => ['nullable', 'max:255'],
            'education.*.still_studying' => ['nullable', 'boolean'],
            'education.*.additonal_information' => ['nullable', 'max:255'],
        ];
    }
}
