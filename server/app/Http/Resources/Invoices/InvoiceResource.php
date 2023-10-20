<?php

namespace App\Http\Resources\Invoices;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Invoices\InvoiceProductsCollection;

class InvoiceResource extends JsonResource
{
  
    public function toArray($request)
    {
        return [
            'company_detail' => [
                'name' => is_null($this->company->name) ? '--' : $this->company->name,
                'website' => is_null($this->company->website_link) ? '--' : $this->company->website_link,
            ],
            'customer_detail' => is_null($this->client_id) ? '--' : [
                'first_name' => is_null($this->client->first_name) ? '0' : $this->client->first_name,
                'last_name' => is_null($this->client->last_name) ? '0' : $this->client->last_name,
                'email' => is_null($this->client->email) ? '0' : $this->client->email,
            ],
            'invoice_id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'order_no' => $this->order_no,
            'invoice_date' => $this->invoice_date,
            'due_date' => is_null($this->due_date) ? '--' :  $this->due_date,
            'sales_person' => is_null($this->sales_person) ? '--' : $this->sales_person,
            'subject' => is_null($this->subject) ? '--' : $this->subject,
            'sub_total' => $this->sub_total,
            'total' => $this->total,
            'discount' => $this->discount,
            'shipping_charged' => $this->shipping_charged,
            'customer_note' => is_null($this->customer_note) ? '' : $this->customer_note,
            'terms_n_conditions' => is_null($this->terms_n_conditions) ? '' : $this->terms_n_conditions,
            'status' => $this->status,
            'tax' => $this->tax,
            'balance' => is_null($this->balance) ? '0' : $this->balance,
            'amount_paid' => is_null($this->amount_paid) ? '0' : $this->amount_paid,
            'invoice_products' => InvoiceProductsCollection::collection($this->products)
        ];
    }
}
