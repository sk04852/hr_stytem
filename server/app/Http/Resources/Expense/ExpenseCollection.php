<?php

namespace App\Http\Resources\Expense;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseCollection extends JsonResource
{
    public function toArray($request)
    {
        return [
                'id' => $this->id,
                'merchant'=> $this->merchant,
                'date'=> $this->date,
                'amount'=> $this->amount,
                'currency'=> $this->currency,
                'image'=> $this->image,
                'description'=> $this->description,
                'created_by_data' => [
                    'id'=> ($this->createdBy !== null)? $this->createdBy->id: 0,
                    'first_name'=> ($this->createdBy !== null)? $this->createdBy->first_name: '',
                    'last_name'=> ($this->createdBy !== null)? $this->createdBy->last_name: '',
                ],
                'reporter_data' => [
                    'id'=> ($this->reportedBy !== null)? $this->reportedBy->id: 0,
                    'first_name'=> ($this->reportedBy !== null)? $this->reportedBy->first_name: '',
                    'last_name'=> ($this->reportedBy !== null)? $this->reportedBy->last_name: '',
                ],
                'category_data' => [
                    'id'=> ($this->expenseCategory !== null)? $this->expenseCategory->id: '',
                    'name'=> ($this->expenseCategory !== null)? $this->expenseCategory->name: '',
                    'bg_color'=> ($this->expenseCategory !== null)? $this->expenseCategory->bg_color: '',
                    'description'=> ($this->expenseCategory !== null)? $this->expenseCategory->description: '',
                ]

        ];
    }

}
