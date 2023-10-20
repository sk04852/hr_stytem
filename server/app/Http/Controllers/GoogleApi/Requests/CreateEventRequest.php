<?php

namespace App\Http\Controllers\GoogleApi\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
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
            'name' => 'required',
            'description' => 'required',
            'is_all_day' => 'nullable|boolean',
            'start_date_time' => 'required|date_format:Y-m-d H:i:s',
            'end_date_time' => 'required|date_format:Y-m-d H:i:s',
            'calendar_id' => 'required|array',
            'calendar_id.*' => 'exists:google_calendars,id',
            'transparency' => 'required'
        ];
    }
}
