<?php

namespace App\Http\Controllers\Estimates\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateEstimateRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {

        return [

            // these rules belongs to invoice_products table
            'products'=> ['required', 'array', "min:1"],
            'note' => ['nullable','array'],

            // these rules belongs to invoices table
            'estimate_number' => ['nullable', Rule::unique('estimates')->where(function($query){
                $query->where('company_id', auth()->user()->company_id);
            })],
            'estimate_date' => ['required', 'date','date_format:Y-m-d'],
            'expiry_date' => ['nullable', 'date','date_format:Y-m-d'],
            'sales_person' => ['nullable', 'exists:users,id'],
            'subject' => ['nullable'],
            'notes' => ['nullable'],
            'terms' => ['nullable'],
            'status' => ['nullable'],
            'client_id' => ['nullable', 'exists:clients,id'],
 
        ];
    }

  
}
