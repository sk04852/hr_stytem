<?php

namespace App\Http\Controllers\Generics\Models;

use App\Http\Controllers\EmsEmployee\Models\Employee;
use App\Models\BaseModel;

class Designation extends BaseModel implements IGeneric
{
    protected $table = 'generic_designations';

    protected $fillable = [
        'company_id',
		'name',
		'param_1',
        'param_2'
    ];

    public function employees()
    {
        return $this->belongsToMany(Employee::class);
    }
}
