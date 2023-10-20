<?php

namespace App\Http\Resources\Estimates\ClientEstimates;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientEstimatesCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'estimate_number' => $this->estimate_number,
            'order_no' => ($this->order_no == null)? "": $this->order_no,
            'payment_type' => $this->payment_type,
            'balance' => $this->balance,
            'amount_paid' => $this->amount_paid,
            'shipping_charges' => $this->shipping_charges,
            'sub_total' => $this->sub_total,
            'total' => $this->total,
            'estimate_date' => $this->estimate_date,
            'expiry_date' => $this->expiry_date,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
