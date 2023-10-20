<?php

namespace App\Http\Controllers\Invoices\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalculateProductBillRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [

            'product_id' => ['required', 'exists:vendor_products,id'],
            'price' => ['required', 'regex:/^\d+(\.\d{1,3})?$/'],
            'quantity' => ['required', 'integer'],
            'discount' => ['nullable'],
 
        ];
    }

    public function messages()
    {
        return [
            'price.regex' => 'Invalid price format',
            'discount.regex' => 'Invalid discount format',
        ];
    }

  
}
