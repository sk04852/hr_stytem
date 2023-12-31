<?php

namespace App\Http\Controllers\Actions\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateActionRequest extends FormRequest
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
            'Action-ID' => 'required|max:20',
            'Name' => ['nullable', 'string'],
        ];
    }
}
