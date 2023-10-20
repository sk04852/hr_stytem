<?php

namespace App\Http\Controllers\job_work_lang\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobWorkLang extends FormRequest
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
          
            'job_ID' => 'nullable',
            'languages' => 'nullable'
        ];
    }
}
