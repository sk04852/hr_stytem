<?php

namespace App\Http\Controllers\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartialUpdateUser extends FormRequest
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
            'name' => 'nullable|max:50',
            'job_title' => 'nullable|max:50',
            'location' => 'nullable|max:50',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|unique:user_pr,email,'. $this->userpr_id,
            'password' => 'nullable|string|min:6|confirmed',
            'timezone_id' => 'nullable|exists:timezones,id',
            'role_id' => 'nullable|integer',
            'zoom_id' => 'nullable|max:191',
            'zoom_personal_link' => 'nullable|max:191',
            'skype_id' => 'nullable|max:191',
            'skype_personal_link' => 'nullable|max:191',
            'google_calendar_access_token' => 'nullable|max:65535',
            'google_calendar_refresh_token' => 'nullable|max:65535',
            'google_calendar_user_account_info' => 'nullable|max:65535'
        ];
    }
}
