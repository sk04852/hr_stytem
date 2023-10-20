<?php

namespace App\Http\Controllers\Tags\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagsRequest extends FormRequest
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
            'id' => 'required|exists:tags,id',
            'name' => ['required', 'string', 'max:255']
        ];
    }
}
