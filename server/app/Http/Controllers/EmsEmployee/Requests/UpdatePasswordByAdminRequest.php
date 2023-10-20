<?php

namespace App\Http\Controllers\EmsEmployee\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordByAdminRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => 'required|exists:employees,id',
            'password' => 'required|min:6,max:20|confirmed',
            'password_confirmation' => 'required|min:6,max:20',
        ];
    }
}
