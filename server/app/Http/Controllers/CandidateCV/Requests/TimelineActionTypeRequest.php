<?php

namespace App\Http\Controllers\CandidateCV\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TimelineActionTypeRequest extends FormRequest
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
            'candidatecv_id' => 'required|exists:candidatecv,id',
            'timeline_action_type_id' => 'required|exists:timeline_action_types,id',
            'timeline_action_type_comment' => 'nullable|max:16777215'
        ];
    }
}
