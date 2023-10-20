<?php

namespace App\Http\Controllers\EmsAttendance\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAttendanceRequest extends FormRequest
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
            'employee_id' => 'required|exists:employees,id',
            'date' => ['required', 'date'],
            'time_in' => ['required', 'date_format:H:i:s'],
            'time_out' => ['nullable', 'date_format:H:i:s','after:time_in'],
            'project' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
            'break_start' => ['nullable',  'date_format:H:i:s'],
            'break_end' => ['nullable',  'date_format:H:i:s','after:break_start'],
            'break_note' => ['nullable', 'string'],
            'total_hours' => ['nullable', 'date'],
            'overtime' => ['nullable', 'string'],
        ];
    }
}
