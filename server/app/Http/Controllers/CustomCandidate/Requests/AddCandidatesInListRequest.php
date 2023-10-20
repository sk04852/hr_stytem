<?php

namespace App\Http\Controllers\CustomCandidate\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddCandidatesInListRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'list_name_id' => 'required|exists:custom_candidate_list_names,id',
            'candidate_ids' => 'required|array',
            'candidate_ids.*' => 'exists:candidatecv,id',

        ];
    }
}
