<?php

namespace App\Http\Controllers\CalendarUser\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateCalendarUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [

            "id" => "required|exists:calendar_users,id",
            "user_id" => "required|exists:users,id",
            "calendar_id" => "required|exists:calendars,id",
            
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
