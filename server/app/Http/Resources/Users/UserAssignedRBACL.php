<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAssignedRBACL extends JsonResource
{

    public function toArray($role)
    {
        $profiles =  $this->profiles;
        $permissions = [];
        foreach($profiles as $key=> $profile) {
            $permissions[] = $profile->permissions;
        }
        return [
            'role_id'=> $this->id,
            'role_name'=> $this->name,
            'profiles'=> $profiles->map(function($profile) { return ["id"=> $profile->id, "name"=> $profile->name];}),
            'permissions'=> $permissions
        ];
    }
}
