<?php

namespace App\Http\Controllers\UserTags\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserTagRequest extends FormRequest
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
            'Tag-ID' => 'required|max:20',
            'Name' => ['nullable', 'string'],
        ];
    }
}
