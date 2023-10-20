<?php
namespace App\Http\Controllers\Clients\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientCreateRequest extends FormRequest
{

   public function authorize()
   {
       return true;
   }


   public function rules()
   {
       return [
            'first_name' => ['required','string','max:20'],
            'last_name' => ['required','string','max:20'],
       		'email' => ['nullable', 'unique:clients','email'],
            'mobile' => ['required','string','max:40'],
       		'currency' => ['required'],
       ];
   }
}

?>
