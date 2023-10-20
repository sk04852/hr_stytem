<?php
namespace App\Http\Controllers\Clients\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientAddressesRequest extends FormRequest
{

   public function authorize()
   {
       return true;
   }


   public function rules()
   {
       return [

            'id' => ['required','exists:client_addresses'],
            'client_id' => ['required','exists:clients,id'],
            'type' => ['required', Rule::in([
                'Billing','Shipping'
            ])],
       ];
   }
}

?>
