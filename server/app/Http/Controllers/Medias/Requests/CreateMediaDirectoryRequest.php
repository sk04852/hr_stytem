<?php namespace App\Http\Controllers\Medias\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateMediaDirectoryRequest extends FormRequest {

    public function authorize(){
        return true;
    }

    public function rules()
	{

        return [
                    "directory_name"=>"required",
               ];
    }

    protected function failedValidation(Validator $validator) { throw new HttpResponseException(response()->json($validator->errors(), 422)); }
}
