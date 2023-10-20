<?php

namespace App\Http\Controllers\Holidays\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateHolidaysRequest extends FormRequest
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

            'title' => ['required','string'],
            'date' => ['required','string'],
            'country_code' => ['required','min:2','max:2','alpha'],

        ];
    }
}
