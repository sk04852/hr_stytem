<?php

namespace App\Http\Controllers\Tools\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateToolRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [

            'name' => ['required','string','max:255',Rule::unique('tools')->where(function($query) {
                $query->where('company_id', '=', auth()->user()->company_id);
             })],

            'url' => ['nullable'],
            'username' => ['nullable'],
            'password' => ['nullable'],
            'description' => ['nullable'],
        ];
    }

    public function messages(){

        return [
            'name.required' => 'The tool name field is required',
            'name.unique' => 'The tool name already exists in the system',
        ];
    }
}
