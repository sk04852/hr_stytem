<?php

namespace App\Http\Controllers\CandidateCVProfiles\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCandidateCVProfileRequest extends FormRequest
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

            'candiadte-ID' => 'required|max:20',
            'Int-code' => 'nullable',
            'Email' => 'nullable',
            'Phone-number' => 'nullable',
            'photo' => 'nullable',
            'Date-added' => 'nullable',
            'Userpr-ID' => 'nullable|exists:user-pr,id',
            'Nr-of-jobs' => 'nullable',
            'Time-from-last act' => 'nullable',
            'Date-of-birth' => 'nullable',
            'Lang-lvl' => 'nullable',
            'file-ID' => 'nullable',
            'Tag-ID' => 'nullable|exists:user-tags,id',
            'skill-iD' => 'nullable|exists:skill,id',
            'Action-iD' => 'nullable|exists:actions,id',
            'consent-iD' => 'nullable',
        ];
    }
}
