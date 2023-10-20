<?php

namespace App\Http\Controllers\TradingPlatforms\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTradingPlatformsRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            "login" => ["required","unique:trading_platforms,login"],
            "password" => ["required"],
            "port" => ["required","numeric"],
            "host" => ["required","string"],
            "environment" => ["required", 'exists:field_options,id'],
            "platform" => ["required", 'exists:field_options,id'],
        ];
    }
}
