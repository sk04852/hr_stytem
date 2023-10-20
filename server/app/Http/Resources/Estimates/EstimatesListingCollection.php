<?php

namespace App\Http\Resources\Estimates;

use Illuminate\Http\Resources\Json\JsonResource;

class EstimatesListingCollection extends JsonResource
{

    public function toArray($request)
    {
        return [

            'id' => $this->id,
            'order_no' => $this->order_no,
            'sub_total' => $this->sub_total,
            'pre_order_date' => $this->pre_order_date,
            'client_detail' => [
                'name' => $this->client->first_name.' '.$this->client->last_name,
                'mobile' => $this->client->mobile,
                'phone' => $this->client->phone,
                'address' => $this->client->address
            ]
        ];
    }
}
