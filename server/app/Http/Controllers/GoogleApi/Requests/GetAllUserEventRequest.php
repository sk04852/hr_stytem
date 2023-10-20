<?php

namespace App\Http\Controllers\GoogleApi\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetAllUserEventRequest extends FormRequest
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
            'calendar_id' => 'required|array',
            'calendar_id.*' => 'exists:google_calendars,id',
        ];
    }
}
