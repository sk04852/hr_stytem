<?php

namespace App\Http\Resources\Employees;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeesCollection extends JsonResource
{
    public function toArray($request)
    {
        return [
                'id' => $this->id,
                'employee_number'=> $this->employee_number,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'email' => $this->user->email,
                'job_id'=> $this->job_id,
                'location_id'=> $this->location_id,
                'policy_id'=> $this->policy_id,
                'status'=> $this->user->status,
        ];
    }

}
