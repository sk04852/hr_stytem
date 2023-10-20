<?php namespace App\Http\Controllers\Accounts\Requests;

use App\Http\Controllers\Users\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class AccountUpdateRequest extends FormRequest {

    public function authorize(){
        return true;
    }

    public function rules()
	{
        return [
                "id" => "required|exists:accounts,id",
                "first_name"=>"required|max:20",
                "account_name"=>"required|max:20",
                "middle_name"=>"max:20",
                "last_name"=>"required|max:20",
                "lead_id" => "nullable|exists:leads,id",
                "email" => ["required","email"],
                "password" => "required|min:6,max:20|confirmed",
                "password_confirmation" => "required|min:6,max:20",
                "assigned_to" => "required|exists:users,id",
                "password" => "nullable|min:6,max:20|confirmed",
                "password_confirmation" => "nullable|min:6,max:20",
                "promo_code" => "nullable|exists:promo_codes",
                "trading_status" => "nullable|exists:field_options,id",
                "secondary_income" => "nullable|exists:field_options,id",
                "secondary_source_of_income" => "nullable|exists:field_options,id",
                "anticipated_account_turnover_annually" => "nullable|exists:field_options,id",
                "fund_method_country" => "nullable|exists:field_options,id",
                "proof_of_residence" => "nullable|exists:field_options,id",
                "proof_of_identity" => "nullable|exists:field_options,id",
                "document_status" => "nullable|exists:field_options,id",
                "account_type_requested" => "nullable|exists:field_options,id",
                "website_language" => "nullable|exists:field_options,id",
                "account_status" => "nullable|exists:field_options,id",
                "id_type" => "nullable|exists:field_options,id",
                "account_stage" => "nullable|exists:field_options,id",
                "client_type" => "nullable|exists:field_options,id",
               ];
    }

    protected function failedValidation(Validator $validator) { throw new HttpResponseException(response()->json($validator->errors(), 422)); }
}
