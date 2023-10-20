<?php

namespace App\Http\Controllers\TimeOff\Models;

use App\Models\BaseModel;

class TimeoffType extends BaseModel
{
    protected $table = 'timeoff_types';
    public static $snakeAttributes = false;
    protected $fillable = [
        'policy_id',
        'company_id',
        'name',
        'is_default',
        'allocation_method',
        'accrual_period',
        'accrual_start_date',
        'accrual_end_date',
        'accrual_allowance',
        'requires_approval',
        'available_period',
        'used_period',
        'percentage_used'
    ];

    public function employeeTimeOffs()
    {
        return $this->hasMany(EmployeeTimeOff::class);
    }
}
