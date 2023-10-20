<?php

namespace App\Http\Controllers\SMSConfigurations\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSmsConfigurationRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [

            'gateway_type' => ['required', Rule::in(
                ['twilio', 'ClickaTell']
            )],
            'credentials' => ['required'],
            'status' => ['required', Rule::in(
                ['Active', 'Deactivated']
            )],

        ];
    }

}
