<?php

namespace App\Http\Controllers\CandidateCV\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateFileRequest extends FormRequest
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
            'files.*' => 'required|file|max:20000'
        ];
    }

    public function messages()
    {
        return [
            'files.*.max' => 'File size should not be more than 20mb'
        ];
    }
}
