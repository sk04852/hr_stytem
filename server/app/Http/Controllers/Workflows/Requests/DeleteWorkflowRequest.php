<?php

namespace App\Http\Controllers\Workflows\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteWorkflowRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            "id" => "required||exists:workflows,id",
        ];
    }
}
