<?php

namespace App\Http\Controllers\HeartBeats\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHeartBeatRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            "id" => "required|exists:heart_beats,id",
            "mathod" => "required",
            "url" => "required",
        ];
    }
}
