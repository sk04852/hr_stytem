<?php

namespace App\Http\Resources\Permissions;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PermissionResourceCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'first_name'=> $this->employee_number,
            'date' => dateFormat($this->created_at)
        ];
    }
}
