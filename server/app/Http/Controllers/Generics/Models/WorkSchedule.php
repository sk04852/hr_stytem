<?php

namespace App\Http\Controllers\Generics\Models;
use App\Models\BaseModel;

class WorkSchedule extends BaseModel implements IGeneric
{
    protected $table = 'generic_work_schedules';

    protected $fillable = [
        'company_id',
		'name',
		'param_1',
        'param_2'
    ];
}
