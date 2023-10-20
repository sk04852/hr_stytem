<?php

namespace App\Http\Resources\Reports;

use App\Http\Controllers\Invoices\Models\Invoice;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Controllers\Generics\Models\Customer;

class ReportByInvoiceNumberResource extends JsonResource
{
    
    public function getTotal($totalType){
        return Invoice::where('invoice_number', $this->transaction_id)->sum($totalType);
    }

    public function toArray($request)
    {
        $productsList = Invoice::where('invoice_number', $this->transaction_id)->get();
        $subTotal = $this->getTotal('total');
        $customer = Customer::find($this->relation_id);
        return [
            
            // invoice data
            'invoice_number' => $this->transaction_id,
            'total' => $subTotal,
            'paid_amount' => $this->credit,
            'created_at' => $this->created_at,
            // customer data
            'customer_detail' => [
                'name' => is_null($customer->name) ? '0' : $customer->name,
                'phone' => is_null($customer->param_1) ? '0' : $customer->param_1,
                'address' => is_null($customer->param_2) ? '0' : $customer->param_2,
            ],
            // purchased products listing
            'products' => ProductsCollection::collection($productsList)
        ];
    }
}
