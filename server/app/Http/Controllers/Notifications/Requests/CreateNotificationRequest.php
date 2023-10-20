<?php

namespace App\Http\Controllers\Notifications\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class CreateNotificationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'receiver_id' => ['required', 'exists:users,id'],
            'type' => ['required', Rule::in(['Email', 'Push Notification', 'SMS', 'Internal'])],
            'body' => ['required', 'string'],
            'payload' => ['nullable'],
        ];
    }

    protected function failedValidation(Validator $validator) { throw new HttpResponseException(response()->json($validator->errors(), 422)); }

}
