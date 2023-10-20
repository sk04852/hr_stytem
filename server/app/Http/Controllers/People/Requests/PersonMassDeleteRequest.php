<?php namespace App\Http\Controllers\People\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class PersonMassDeleteRequest extends FormRequest {
    public function authorize(){
        return true;
    }

    public function rules()
	{
        return [
                    "id"=>"required|array",
               ];
    }

    protected function failedValidation(Validator $validator) { throw new HttpResponseException(response()->json($validator->errors(), 422)); }
}
