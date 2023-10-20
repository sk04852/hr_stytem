<?php
namespace App\Http\Controllers\Clients\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientChangePasswordRequest extends FormRequest
{
   
   public function authorize()
   {
       return true;
   }

  
   public function rules()
   {
       return [

            'id'=>['required','exists:clients,id'],
            'password' => ['required' ,'min:6', 'max:20', 'confirmed'],
            'password_confirmation' => ['required', 'min:6', 'max:20'],
       ];
   }
}

?>
