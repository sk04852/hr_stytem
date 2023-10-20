<?php namespace App\Http\Controllers\Leads\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class DeleteLeadCommentRequest extends FormRequest {

    public function authorize(){
        return true;
    }

    public function rules()
	{
        return [
                    "id"=>"required|exists:lead_comments,id"
               ];
    }

    protected function failedValidation(Validator $validator) { throw new HttpResponseException(response()->json($validator->errors(), 422)); }
}
