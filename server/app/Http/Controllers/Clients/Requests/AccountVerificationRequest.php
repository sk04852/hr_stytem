<?php
namespace App\Http\Controllers\Clients\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountVerificationRequest extends FormRequest
{
   
   public function authorize()
   {
       return true;
   }

  
   public function rules()
   {
       return [

       		'email' => ['required', 'email'],
            'code' => ['required'],
       ];
   }
}

?>
