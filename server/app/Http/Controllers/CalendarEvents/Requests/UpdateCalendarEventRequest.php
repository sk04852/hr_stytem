<?php

namespace App\Http\Controllers\CalendarEvents\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateCalendarEventRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "id" => "required|exists:calendar_events,id",
            "calendar_id" => "nullable|exists:calendars,id",
            "subject"=>"required",
            "type"=>"required",
            "activity_type"=>"required",
            "start_date_time"=>"required|date_format:Y-m-d H:i:s",
            "end_date_time"=>"nullable|date_format:Y-m-d H:i:s|after:start_date_time",
            "visibility"=>"required",
            "status"=>"required",
            "assigned_to"=>"nullable|exists:users,id"
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
