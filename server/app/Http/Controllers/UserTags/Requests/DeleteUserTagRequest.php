<?php

namespace App\Http\Controllers\UserTags\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteUserTagRequest extends FormRequest
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
            'id' => ['required', 'exists:user-tags,id'],
        ];
    }
}
