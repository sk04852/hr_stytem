<?php

namespace App\Http\Controllers\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
            'name' => 'required|max:50',
            'photo' => 'image',
            'phone' => 'required|max:20',
            'job_title' => 'max:50',
            'location' => 'max:50',
            'timezone_id' => 'exists:timezones,id',
            'int_code' => 'max:5'
        ];
    }
}
