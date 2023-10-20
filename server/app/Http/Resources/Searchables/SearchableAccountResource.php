<?php

namespace App\Http\Resources\Searchables;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchableAccountResource extends JsonResource
{
    public function toArray($account) {
        return [
            'id'=> $this->id,
            'text'=> $this->getSearchableText(),
            "attributes"=> $this->getSearchableAttributes()
        ];
    }

    public function getSearchableText() {
        return "UserId-{$this->user->id} AccountId-{$this->id} {$this->user->first_name} {$this->user->last_name} {$this->user->email} {$this->account_name}";
    }

    public function getSearchableAttributes() {
        return [
            "user_id"=> $this->user->id,
            "company_id"=> $this->user->company_id,
            "account_id"=> $this->id,
            "first_name"=> $this->user->first_name,
            "last_name"=> $this->user->last_name,
            "email"=> $this->user->email,
            "account_name"=> $this->account_name,
            "assigned_to"=> $this->assignedTo->first_name . " ". $this->assignedTo->last_name,
            "brand_id"=> $this->brand->id,
            "brand_name"=> $this->brand->name
        ];
    }

}
