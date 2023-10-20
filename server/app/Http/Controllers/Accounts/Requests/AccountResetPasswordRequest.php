<?php
namespace App\Http\Controllers\Accounts\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class AccountResetPasswordRequest extends FormRequest
{
   /**
    * Determine if the user is authorized to make this request.
    *
    * @return bool
    */
   public function authorize()
   {
       return true;
   }

   /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
   public function rules()
   {
       return [
        "token" => "required|min:6,max:20|exists:accounts,password_recovery_token",
        "password" => "required|min:6,max:20|confirmed",
        "password_confirmation" => "required|min:6,max:20"
       ];
   }

   protected function failedValidation(Validator $validator) {
    throw new HttpResponseException(response()->json($validator->errors(), 422));
 }
}

?>
