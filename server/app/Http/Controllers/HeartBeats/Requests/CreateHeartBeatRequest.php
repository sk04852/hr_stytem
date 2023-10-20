<?php

namespace App\Http\Controllers\HeartBeats\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateHeartBeatRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            "method" => "required",
            "url" => "required",
            "execute_after" => "required",
            "execute_after_error" => "required",
        ];
    }
}
