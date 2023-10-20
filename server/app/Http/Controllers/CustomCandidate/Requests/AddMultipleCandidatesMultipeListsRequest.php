<?php

namespace App\Http\Controllers\CustomCandidate\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddMultipleCandidatesMultipeListsRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'custom_lists' => 'required|array',
            'custom_lists.*.id' => 'nullable|exists:custom_candidate_list_names,id',
            'custom_lists.*.name' => 'nullable|max:191',
            'candidate_ids' => 'required|array',
            'candidate_ids.*' => 'exists:candidatecv,id',

        ];
    }
}
