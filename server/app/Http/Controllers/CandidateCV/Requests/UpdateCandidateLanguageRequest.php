<?php

namespace App\Http\Controllers\CandidateCV\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCandidateLanguageRequest extends FormRequest
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
            'candidatecv_id' => ['required', Rule::exists('candidatecv', 'id')->whereNull('deleted_at')],
            'language' => 'required|array',
            'language.*.id' => ['required', Rule::exists('languages', 'id')->whereNull('deleted_at')],
            'language.*.speaking' => 'required|max:255',
            'language.*.reading_writing' => 'required|max:255'
        ];
    }
}
