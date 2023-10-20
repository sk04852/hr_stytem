<?php

namespace App\Http\Resources\Reports;

use App\Http\Controllers\Generics\Models\Customer;
use App\Http\Controllers\Invoices\Models\Invoice;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportsCollection extends JsonResource
{

    public function getTotal($transactionId){
        return Invoice::where('invoice_number', $transactionId)->sum('total');
    }

    public function toArray($request)
    {
        $productsList = Invoice::where('invoice_number', $this->transaction_id)->get();
        $subTotal = $this->getTotal($this->transaction_id);
        $customer = Customer::find(1);
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
