<?php

namespace App\Http\Resources\Groups;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupsResourceCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            
            'id' => $this->id,
            'group_name' => $this->group_name,
            'date' => dateFormat($this->created_at)
        ];
    }
}
