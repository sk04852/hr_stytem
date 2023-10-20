<?php

namespace App\Http\Resources\RBACL;

use Illuminate\Http\Resources\Json\JsonResource;

class RolesCollection extends JsonResource
{
    public function toArray($request)
    {
        return [
                'id' => $this->id,
                'name'=> $this->name,
                'profiles'=> $this->profiles
        ];
    }

}
