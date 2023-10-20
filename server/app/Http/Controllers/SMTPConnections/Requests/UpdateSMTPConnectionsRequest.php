<?php

namespace App\Http\Controllers\SMTPConnections\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;


class UpdateSMTPConnectionsRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            "id" => "required|exists:smtp_connections,id",
            'title' => ['required','string',Rule::unique('smtp_connections')->ignore($this->id)],
            'server_name' => 'required',
            'mail_host' => 'required',
            'port' => 'required',
            'user_name' => 'required',
            'password' => 'required',
            'from_email' => 'required',
            'requires_authentication' => 'required'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
