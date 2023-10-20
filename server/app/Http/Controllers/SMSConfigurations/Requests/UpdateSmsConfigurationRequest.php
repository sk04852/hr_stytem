<?php

namespace App\Http\Controllers\SMSConfigurations\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSmsConfigurationRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [

            'id' => ['required', 'exists:sms_configurations'],
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
