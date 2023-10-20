<?php

namespace App\Http\Controllers\Workflows\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateWorkflowRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            "module_id" => "required",
            "description" => "required",
            "execution" => ['required', Rule::in(['first save', 'first time condition', 'everytime saved', 'everytime modified', 'schedule'])]
        ];        
    }
}
