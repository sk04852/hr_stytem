<?php

namespace App\Http\Controllers\Invoices\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvoiceRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }
    
    public function rules()
    {

        return [

            // these rules belongs to invoices table
            'invoice_id' => ['required','exists:invoices,id'],
            'products'=> ['required', 'array'],
            'note' => ['nullable','array'],
            'client_id' => ['required','exists:clients,id'],
            'type' => ['required',Rule::in([
                'Credit',
                'Debit',
                'Cash']
            )],
            'invoice_date' => ['required', 'date','date_format:Y-m-d'],
            'due_date' => ['nullable', 'date','date_format:Y-m-d'],
            'sales_person' => ['nullable', 'exists:users,id'],
            'subject' => ['nullable'],
            'notes' => ['nullable'],
            'terms' => ['nullable'],
            'status' => ['nullable',Rule::in([
                'Pending',
                'Paid',
                'Cancelled']
            )],
            'client_id' => ['nullable', 'exists:clients,id']
 
        ];
    }

  
}
