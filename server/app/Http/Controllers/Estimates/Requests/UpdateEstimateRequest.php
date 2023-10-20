<?php

namespace App\Http\Controllers\Estimates\Requests;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEstimateRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {

        return [

            // these rules belongs to pre_order_products table
            'products'=> ['required', 'array', "min:1"],
            'note' => ['nullable','array'],

            // these rules belongs to pre orders table
            'pre_order_id' => ['required','exists:pre_orders,id'],
            'pre_order_date' => ['required', 'date','date_format:Y-m-d'],
            'due_date' => ['nullable', 'date','date_format:Y-m-d'],
            'sales_person' => ['nullable', 'exists:users,id'],
            'subject' => ['nullable'],
            'notes' => ['nullable'],
            'terms' => ['nullable'],
            'client_id' => ['nullable', 'exists:clients,id'],
 
        ];
    }
  
}
