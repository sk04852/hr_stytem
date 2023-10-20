<?php namespace App\Http\Controllers\Leads\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class UpdateLeadRequest extends FormRequest {

    public function authorize(){
        return true;
    }

    public function rules()
	{
        return [
                    "id"=>"required|exists:leads,id",
                    "first_name"=>"required",
                    "last_name"=>"required",
                    "brand_id" => "nullable|exists:brands,id",
                    "lead_id" => ["nullable",Rule::unique('leads')->ignore($this->request->get('id'))],
                    "primary_email" => "nullable|email",
                    "secondary_email" => "nullable|email",
                    "is_opt_out" => "nullable|boolean",
                    "lead_status" => "nullable|exists:field_options,id",
                    "website_language" => "nullable|exists:field_options,id",
                    'registration_country'=> 'nullable|exists:field_options,id',
                    "spoken_language" => "nullable|exists:field_options,id",
                    "assigned_to" => "nullable|exists:users,id",

               ];
    }

    protected function failedValidation(Validator $validator) { throw new HttpResponseException(response()->json($validator->errors(), 422)); }
}
