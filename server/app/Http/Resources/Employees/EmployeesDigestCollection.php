<?php

namespace App\Http\Resources\Employees;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeesDigestCollection extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id'=> $this->user->id,
            'employee_number'=> $this->employee_number,
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'email' => $this->user->email,
        ];
    }
}
