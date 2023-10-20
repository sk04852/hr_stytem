<?php

namespace App\Http\Controllers\CustomCandidate\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCandidateCustomListRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'list_name_id' => 'required|exists:custom_candidate_list_names,id',
            'list_name'    => 'nullable',
            'candidate_ids' => 'required|array',
            'candidate_ids.*' => 'exists:candidatecv,id',

        ];
    }
}
