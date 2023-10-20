<?php namespace App\Http\Controllers\Accounts\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class TransferOwnershipRequest extends FormRequest {

    public function authorize(){
        return true;
    }

    public function rules()
	{

        return [
                    "owner_id"=>"required|exists:users,id",
                    "accounts_id"=>"required|array",
               ];
    }

    protected function failedValidation(Validator $validator) { throw new HttpResponseException(response()->json($validator->errors(), 422)); }
}
