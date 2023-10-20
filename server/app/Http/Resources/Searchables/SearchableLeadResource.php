<?php

namespace App\Http\Resources\Searchables;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchableLeadResource extends JsonResource
{
    public function toArray($account) {
        return [
            'id'=> $this->id,
            'text'=> $this->getSearchableText(),
            "attributes"=> $this->getSearchableAttributes()
        ];
    }

    public function getSearchableText() {
        return "LeadId-{$this->id} {$this->first_name} {$this->last_name} {$this->primary_email} {$this->mobile}";
    }

    public function getSearchableAttributes() {
        return [
            "lead_id"=> $this->id,
            "brand_id"=> ($this->brand != null)? $this->brand->id: null,
            "company_id"=> ($this->brand != null)? $this->brand->company_id: null,
            "first_name"=> $this->first_name,
            "last_name"=> $this->last_name,
            "email"=> $this->primary_email,
            "mobile"=> $this->mobile,
            "brand_name"=> ($this->brand != null)? $this->brand->name: null
        ];
    }

}
