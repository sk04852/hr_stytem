<?php namespace App\Http\Controllers\Leads\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class LeadMassCommentRequest extends FormRequest {

    public function authorize(){
        return true;
    }

    public function rules()
	{
        return [
                    "lead_id"=>"required|exists:leads,id|array",
                    "comment"=>"required"
               ];
    }

    protected function failedValidation(Validator $validator) { throw new HttpResponseException(response()->json($validator->errors(), 422)); }
}
