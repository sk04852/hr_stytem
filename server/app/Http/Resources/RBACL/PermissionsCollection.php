<?php

namespace App\Http\Resources\RBACL;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionsCollection extends JsonResource
{
    public function toArray($request)
    {
        return [
                'id' => $this->id,
                'name'=> $this->name,
                'permission'=> $this->permissions
        ];
    }

}
