<?php

namespace App\Http\Controllers\Areas\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAreaRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        

        return [
            
            'id' => ['required','exists:areas,id'],
            'params1' => ['nullable','string','max:255'],
            'params2' => ['nullable','string','max:255'],
        
        ];
    }

   
}
