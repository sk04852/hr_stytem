<?php

namespace App\Http\Resources\Estimates;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Estimates\EstimateProductsCollection;

class EstimateResource extends JsonResource
{
  
    public function toArray($request)
    {
        return [
            
            'company_detail' => [
                'name' => is_null($this->company->name) ? '--' : $this->company->name,
                'website' => is_null($this->company->website_link) ? '--' : $this->company->website_link,
            ],
            'customer_detail' => [
                'first_name' => is_null($this->client->first_name) ? '0' : $this->client->first_name,
                'last_name' => is_null($this->client->last_name) ? '0' : $this->client->last_name,
                'email' => is_null($this->client->email) ? '0' : $this->client->email,
            ],
            'order_no' => $this->order_no,
            'pre_order_date' => $this->pre_order_date,
            'due_date' => is_null($this->due_date) ? '--' :  $this->due_date,
            'sales_person' => is_null($this->sales_person) ? '--' : $this->sales_person,
            'subject' => is_null($this->subject) ? '--' : $this->subject,
            'sub_total' => $this->sub_total,
            'discount' => $this->discount,
            'shipping_charged' => $this->shipping_charged,
            'notes' => is_null($this->notes) ? '--' : $this->notes,
            'terms' => is_null($this->terms) ? '--' : $this->terms,
            'status' => $this->status,
            'balance' => is_null($this->balance) ? '0' : $this->balance,
            'amount_paid' => is_null($this->amount_paid) ? '0' : $this->amount_paid,
            'order_products' => EstimateProductsCollection::collection($this->products)
        ];
    }
}
