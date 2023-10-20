<?php namespace App\Http\Controllers\Leads\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class DeleteLeadRequest extends FormRequest {

    public function authorize(){
        return true;
    }

    public function rules()
	{
        return [
                    "id"=>"required|exists:leads,id"
               ];
    }

    protected function failedValidation(Validator $validator) { throw new HttpResponseException(response()->json($validator->errors(), 422)); }
}
