<?php

namespace App\Http\Controllers\Languages\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLanguagesRequest extends FormRequest
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
//            'Language-ID' => 'required|max:20',
            'name' => ['required', 'string'],
        ];
    }
}
