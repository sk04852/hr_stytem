<?php

namespace App\Http\Controllers\GoogleApi\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SharedCalendarWithRequest extends FormRequest
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
            'google_calendar_id' => ['required', Rule::exists('google_calendars', 'id')->whereNull('deleted_at')],
        ];
    }
}
