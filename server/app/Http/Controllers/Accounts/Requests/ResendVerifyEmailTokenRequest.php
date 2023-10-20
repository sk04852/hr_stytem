<?php namespace App\Http\Controllers\Accounts\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ResendVerifyEmailTokenRequest extends FormRequest {

    public function authorize(){
        return true;
    }

    public function rules()
	{

        return [
                    "email" => "required|email|max:150|exists:accounts,email",
               ];
    }

    protected function failedValidation(Validator $validator) { throw new HttpResponseException(response()->json($validator->errors(), 422)); }
}
