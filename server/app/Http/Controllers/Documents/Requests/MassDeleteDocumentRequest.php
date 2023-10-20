<?php namespace App\Http\Controllers\Documents\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class MassDeleteDocumentRequest extends FormRequest {

    public function authorize(){
        return true;
    }

    public function rules()
	{

        return [
                    "document_id"=>"required|array",
               ];
    }

    protected function failedValidation(Validator $validator) { throw new HttpResponseException(response()->json($validator->errors(), 422)); }
}
