<?php

namespace App\Http\Controllers\CustomCandidate\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCandidateCustomListRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'list_name'    => 'required',
            'candidate_ids' => 'required|array',
            'candidate_ids.*' => 'exists:candidatecv,id',

        ];
    }
}
