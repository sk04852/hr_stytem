<?php

namespace App\Http\Controllers\Timelines\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTimelineRequest extends FormRequest
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
            'id' => 'required|exists:timeline,id',
            'Action-ID' => 'required',
            'candidate-ID' => 'nullable|exists:candidatecv,id',
            'Userpr-ID' => 'nullable|exists:user-pr,id',
            'Action-name' => 'nullable',
            'Date-added' => 'nullable',
            'comments' => 'nullable',
        ];
    }
}
