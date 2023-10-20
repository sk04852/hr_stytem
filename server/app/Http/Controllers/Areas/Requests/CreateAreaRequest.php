<?php

namespace App\Http\Controllers\Areas\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAreaRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            
            'name' => ['required','string','max:150'],
            'params1' => ['nullable','string'],
            'params2' => ['nullable','string'],
        ];
    }

   
}
