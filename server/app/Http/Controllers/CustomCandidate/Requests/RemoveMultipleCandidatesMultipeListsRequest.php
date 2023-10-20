<?php

namespace App\Http\Controllers\CustomCandidate\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RemoveMultipleCandidatesMultipeListsRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'list_name_ids' => 'required|array',
            'list_name_ids.*' => 'exists:custom_candidate_list_names,id',
            'candidate_ids' => 'required|array',
            'candidate_ids.*' => 'exists:candidatecv,id',

        ];
    }
}
