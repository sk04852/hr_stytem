<?php

namespace App\Http\Controllers\CandidateCV\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetJobAppliedRequest extends FormRequest
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
            'id' => ['required', Rule::exists('candidatecv', 'id')->whereNull('deleted_at')],
            'action_id' => ['nullable', Rule::exists('actions', 'id')->whereNull('deleted_at')],
        ];
    }
}
