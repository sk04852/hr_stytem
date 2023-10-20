<?php
namespace App\Http\Controllers\Clients\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'password' => ['required' ,'min:6', 'max:20', 'confirmed'],
            'password_confirmation' => ['required', 'min:6', 'max:20'],
       ];
   }
}

?>
