<?php

namespace App\Http\Controllers\CandidateCV\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShowCandidateCVRequest extends FormRequest
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
            'include' => 'nullable|array',
            'include.*' => 'in:nationalities,driving_licenses,recommendations,tags,jobHistory,education,additionalCourses,skills,languages,files'
        ];
    }
}
