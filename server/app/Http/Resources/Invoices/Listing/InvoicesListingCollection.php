<?php

namespace App\Http\Resources\Invoices\Listing;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoicesListingCollection extends JsonResource
{

    public function toArray($request)
    {
        return [

            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'order_no' => ($this->order_no == null)? "": $this->order_no,
            'payment_type' => $this->payment_type,
            'balance' => $this->balance,
            'amount_paid' => $this->amount_paid,
            'shipping_charges' => $this->shipping_charges,
            'sub_total' => $this->sub_total,
            'total' => $this->total,
            'invoice_date' => $this->invoice_date,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'client' => [
                'id' => ($this->client != null)? $this->client->id: 0,
                'name' => ($this->client != null)? $this->client->first_name.' '.$this->client->last_name: "",
                'mobile' => ($this->client != null)? $this->client->mobile: "",
                'phone' => ($this->client != null)? $this->client->phone: "",
                'address' => ($this->client != null)? $this->client->address: ""
            ]
        ];
    }
}
