<?php

namespace App\Http\Resources\Invoices;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceProductsCollection extends JsonResource
{
   
    public function toArray($request)
    {
        return [

            'id' => $this->id,
            'product_name' => is_null($this->product_name) ? $this->product->name : $this->product_name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'discount' => $this->discount,
            'total' => $this->total,
            'sub_total' => $this->sub_total,
            'customer_note' => is_null($this->customer_note) ? '' : $this->customer_note
        ];
    }
}
