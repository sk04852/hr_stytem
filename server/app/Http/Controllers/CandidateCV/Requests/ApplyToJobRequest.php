<?php

namespace App\Http\Controllers\CandidateCV\Requests;

use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Http\Controllers\Jobs\Models\JobPr;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApplyToJobRequest extends FormRequest
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
            'candidatecv_id' => ['required', Rule::exists('candidatecv', 'id')->where('status', CandidateCV::STATUS_ACTIVE)->whereNull('deleted_at')],
            'job_pr_id' => ['required', Rule::exists('jobs_pr', 'id')->where('status', JobPr::STATUS_ACTIVE)->whereNull('deleted_at')],
            'action_id' => ['required', Rule::exists('actions', 'id')->whereNull('deleted_at')],
        ];
    }

    public function messages()
    {
        return [
            // 'candidatecv_id.required' => '',
            // 'candidatecv_id.exists' => 'Candidate Profile is not confirmed',
            'candidatecv_id.exists' => 'Kandidaadi profiil ei ole kinnitatud',
            // 'job_pr_id.required' => ,
            'job_pr_id.exists' => 'Job status is not active',
            // 'action_id.required' => ,
            // 'action_id.exists' => ,
        ];
    }
}
