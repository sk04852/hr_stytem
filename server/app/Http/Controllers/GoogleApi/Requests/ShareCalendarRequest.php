<?php

namespace App\Http\Controllers\GoogleApi\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShareCalendarRequest extends FormRequest
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
            'calendar_id' => 'required|exists:google_calendars,id',
            'user_pr_id' => 'required|array',
            'user_pr_id.*' => 'exists:user_pr,id',
            'role' => 'required|max:50'
        ];
    }
}
