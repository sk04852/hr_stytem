<?php
namespace App\Http\Controllers\Clients\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
{

   public function authorize()
   {
       return true;
   }


   public function rules()
   {
       return [

            'id'=>['required','exists:clients,id'],
            'first_name' => ['required','string','max:40'],
            'last_name' => ['required','string','max:40'],
       		'email' => ['nullable', 'email',Rule::unique('clients')->ignore($this->id)],
            'password' => ['nullable' ,'min:6', 'max:20', 'confirmed'],
            'password_confirmation' => ['nullable', 'min:6', 'max:20'],
            'middle_name' => ['nullable','string','max:40'],
            'username' => ['nullable','string','max:40'],
            'phone' => ['nullable','string','max:40'],
            'mobile' => ['required','string','max:40'],
            'address' => ['nullable','string','max:40'],
            'city' => ['nullable','string','max:40'],
            'state' => ['nullable','string','max:40'],
            'zip' => ['nullable','string','max:40'],

       ];
   }
}

?>
