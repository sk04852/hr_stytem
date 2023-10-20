<?php

namespace App\Http\Resources\TimeoffRequests;

use Illuminate\Http\Resources\Json\JsonResource;

class TimeoffRequestsCollection extends JsonResource
{
    public function toArray($request)
    {
        return [
                'id' => $this->id,
                'employee'=> [
                                "first_name"=> $this->employee->user->first_name,
                                "last_name"=> $this->employee->user->last_name,
                                "email"=> $this->employee->user->email,
                ],
                'from_date'=> $this->from_date,
                'to_date'=> $this->to_date,
                'absent_days'=> $this->absent_days,
                'timeoff_type_name'=> $this->type->name,
                'status'=> $this->status,
        ];
    }
    
}
