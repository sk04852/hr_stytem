<?php

namespace App\Http\Controllers\CandidateCV\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAgreementRequest extends FormRequest
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
            'user_pr_id' => 'required|exists:user_pr,id',
            'files' => 'required|array',
            'files.*' => 'file'
        ];
    }
}
