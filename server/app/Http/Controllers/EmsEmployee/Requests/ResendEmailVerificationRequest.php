<?php

namespace App\Http\Controllers\EmsEmployee\Requests;

use Illuminate\Foundation\Http\FormRequest;


class ResendEmailVerificationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|max:150|exists:users,email',
        ];
    }

}
