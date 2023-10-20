<?php

namespace App\Http\Controllers\TradingPlatforms\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTradingPlatformsRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            "id" => ["required","exists:trading_platforms,id"],
            "login" => ["required",Rule::unique('trading_platforms')->where(function($query){
                                        $query->where('login', '!=', $this->login);})],
            "password" => ["required"],
            "port" => ["required","numeric"],
            "host" => ["required","string"],
            "environment" => ["required", 'exists:field_options,id'],
            "platform" => ["required", 'exists:field_options,id'],
        ];
    }
}
