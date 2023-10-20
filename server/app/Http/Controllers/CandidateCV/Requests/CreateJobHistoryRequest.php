<?php

namespace App\Http\Controllers\CandidateCV\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateJobHistoryRequest extends FormRequest
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
            'jobs' => ['required', 'array'],
            'jobs.*.candidatecv_id' => ['required', Rule::exists('candidatecv', 'id')->whereNull('deleted_at')],
            'jobs.*.company_name' => ['required', 'max:100'],
            'jobs.*.designation' => ['required', 'max:50'],
            'jobs.*.starting_year' => ['required', 'digits:4', 'integer', 'min:1900', 'max:2050'],
            'jobs.*.starting_month' => ['nullable', 'digits_between:1,2', 'integer', 'between:1,12'],
            'jobs.*.starting_day' => ['nullable', 'digits_between:1,2', 'between:01,31'],
            'jobs.*.ending_year' =>  ['exclude_if:jobs.*.still_working,1', 'required', 'digits:4', 'integer', 'min:1900', 'max:2050'],
            'jobs.*.ending_month' =>  ['nullable', 'digits_between:1,2', 'integer', 'between:1,12'],
            'jobs.*.ending_day' => ['nullable', 'digits_between:1,2', 'between:01,31'],
            'jobs.*.still_working' => ['boolean'],
            'jobs.*.description' => ['nullable', 'max:65535'],
            'jobs.*.work_place' => ['nullable', 'max:255']
        ];
    }
}
