<?php
namespace App\Http\Controllers\Deposits\Requests;

use Pearl\RequestValidate\RequestAbstract;

class DepositRequest extends RequestAbstract {

    public function __construct(){

    }

    public function authorize(){
        return true;
    }

    public function rules()
	{
        return [
                    "method_id"=>"required",
                    "amount"=>"required|regex:/^\d+(\.\d{1,2})?$/",
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
