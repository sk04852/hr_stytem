<?php

namespace App\Http\Controllers\CandidateCV\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJobHistoryRequest extends FormRequest
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
            'id' => ['required', Rule::exists('candidatecv_job_history', 'id')->whereNull('deleted_at')],
            'candidatecv_id' => ['required', Rule::exists('candidatecv', 'id')->whereNull('deleted_at')],
            'company_name' => ['required', 'max:100'],
            'designation' => ['required', 'max:50'],
            'starting_year' => ['required', 'digits:4', 'integer', 'min:1900', 'max:2050'],
            'starting_month' => ['nullable', 'digits_between:1,2', 'integer', 'between:1,12'],
            'starting_day' => ['nullable', 'digits_between:1,2', 'between:01,31'],
            'ending_year' =>  ['exclude_if:still_working,1', 'required', 'digits:4', 'integer', 'min:1900', 'max:2050'],
            'ending_month' =>  ['nullable', 'digits_between:1,2', 'integer', 'between:1,12'],
            'ending_day' => ['nullable', 'digits_between:1,2', 'between:01,31'],
            'still_working' => ['boolean'],
            'description' => ['nullable', 'max:65535'],
            'work_place' => ['nullable', 'max:255']
        ];
    }
}
