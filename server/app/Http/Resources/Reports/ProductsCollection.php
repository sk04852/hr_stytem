<?php

namespace App\Http\Resources\Reports;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductsCollection extends JsonResource
{
    
    public function toArray($request)
    {
        return [
            'name' => $this->product->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'discount' => is_null($this->product->discount_price) ? '0' : $this->product->discount_price,
            'tax' => is_null($this->product->vat_code_id) ? '0' : $this->product->vat->name.'%',
            'total' => $this->payable_amount,
        ];
    }
}
