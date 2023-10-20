<?php

namespace App\Http\Resources\Documents;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentsCollection extends JsonResource
{
    public function toArray($request)
    {
        return [
                'id' => $this->id,
                'employee'=> ($this->getEmployee() !== NULL)? $this->getEmployee()->user->first_name: ''
        ];
    }
    
}
