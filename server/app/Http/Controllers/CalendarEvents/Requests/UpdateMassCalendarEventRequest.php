<?php

namespace App\Http\Controllers\CalendarEvents\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateMassCalendarEventRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "id" => "required|array|exists:calendar_events,id",
            "calendar_id" => "required|exists:calendars,id",
            "subject"=>"required|array",
            "event_type"=>"required|array",
            "activity_type"=>"required|array",
            "start_date_time"=>"required|array",
            "end_date_time"=>"required|array",
            "visibility"=>"required|array",
            "status"=>"required|array",
            "assigned_to"=>"required|exists:users,id|array",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
