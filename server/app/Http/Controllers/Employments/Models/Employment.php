<?php

namespace App\Http\Controllers\Employments\Models;

use Illuminate\Database\Eloquent\Model;

class Employment extends Model
{
    protected $table ='employments';

    protected $fillable =[
        'employee_id',
        'position_id',
        'location_id',
        'team_id',
        'probation_start',
        'probation_end',
        'employment_start_date',
        'is_primary_job',
        'employment_end_date',
        'work_schedule_id',
    ];
}
