<?php
namespace App\Http\Controllers\Tickets\Requests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
   public function authorize()
   {
     return true;
   }

   public function rules()
   {
     return [
        "id"=>"required|exists:tickets,id",
        "title" => "required|min:3,max:255",
        "status" => "required|exists:field_options,id",
        "assigned_to" => "required|exists:users,id",
     ];
   }

   protected function failedValidation(Validator $validator)
   {
       throw new HttpResponseException(response()->json($validator->errors(), 422));
   }
 }

 ?>
