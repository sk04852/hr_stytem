<?php

namespace App\Http\Controllers\SMTPConnections\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateMassSMTPConnectionsRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            "id" => "required|array|exists:smtp_connections,id",
            'server_name' => 'required|array',
            'mail_host' => 'required|array',
            'port' => 'required|array',
            'user_name' => 'required|array',
            'password' => 'required|array',
            'from_email' => 'required|array',
            'requires_authentication' => 'required|array'

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
