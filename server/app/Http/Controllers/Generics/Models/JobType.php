<?php

namespace App\Http\Controllers\Generics\Models;
use App\Models\BaseModel;

class JobType extends BaseModel implements IGeneric
{
    protected $table = 'generic_job_types';

    protected $fillable = [
        'company_id',
		'name',
		'param_1',
        'param_2'
    ];
}
