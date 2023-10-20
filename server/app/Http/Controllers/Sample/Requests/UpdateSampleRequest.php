<?php

namespace App\Http\Controllers\Sample\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSampleRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
        ];
    }
}
