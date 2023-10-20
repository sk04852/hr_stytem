<?php

namespace App\Http\Controllers\Invoices\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateInvoiceRequest extends FormRequest
{
    private $checkInvoiceNumberForOutlet = false;
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'products'=> ['required', 'array', "min:1"],
            'note' => ['nullable','array'],
            'client_id' => ['required','exists:clients,id'],
            'type' => ['required',Rule::in([
                'Credit',
                'Debit',
                'Cash']
            )],
            'invoice_number' => ['required', Rule::unique('invoices')->where(function($query) {
                $query->where('company_id', '!=', $this->company_id);
                if($this->checkInvoiceNumberForOutlet) {
                    $outletId = $this->header('outlet-id');
                    if(!is_null($outletId) && $outletId >0) {
                        $query->where('outlet_id', '!=', $outletId);
                    }
                }
             })],
            'invoice_date' => ['required', 'date','date_format:Y-m-d'],
            'due_date' => ['nullable', 'date','date_format:Y-m-d'],
            'sales_person' => ['nullable', 'exists:users,id'],
            'subject' => ['nullable'],
            'notes' => ['nullable'],
            'terms_n_conditions' => ['nullable'],
            'customer_note' => ['nullable'],
            'status' => ['nullable',Rule::in([
                'Pending',
                'Paid',
                'Cancelled']
            )],
            'client_id' => ['nullable', 'exists:clients,id']
        ];
    }


}
