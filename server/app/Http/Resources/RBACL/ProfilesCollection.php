<?php

namespace App\Http\Resources\RBACL;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfilesCollection extends JsonResource
{
    public function toArray($request)
    {
        return [
                'id' => $this->id,
                'name'=> $this->name,
                'permissions'=> $this->permissions
        ];
    }

}
