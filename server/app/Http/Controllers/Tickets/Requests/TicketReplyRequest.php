<?php
namespace App\Http\Controllers\Tickets\Requests;

use Pearl\RequestValidate\RequestAbstract;

class TicketReplyRequest extends RequestAbstract
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
       "ticket_subject" => "required|min:5,max:255",
       "note" => "required",
       "parent_id" => "required|numeric"
     ];
   }

   /**
    * Get custom messages for validator errors.
    *
    * @return array
    */
   public function messages(): array
   {
     return [];
   }
 }

 ?>