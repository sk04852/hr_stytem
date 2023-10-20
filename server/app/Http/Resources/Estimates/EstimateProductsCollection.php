<?php

namespace App\Http\Resources\Estimates;

use Illuminate\Http\Resources\Json\JsonResource;

class EstimateProductsCollection extends JsonResource
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
            'note' => is_null($this->note) ? '--' : $this->note
        ];
    }
}
