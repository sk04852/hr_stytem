<?php

namespace App\Http\Controllers\EventUsers\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateMassEventUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "id" => "required|exists:event_users,id|array",
            "event_id"=>"required|array",
            "user_id"=>"required|array",
            "invited_by"=>"required|array"

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
