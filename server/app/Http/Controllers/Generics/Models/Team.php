<?php

namespace App\Http\Controllers\Generics\Models;

use App\Http\Controllers\AssignEmployees\Models\AssignEmployee;
use App\Http\Controllers\AssignProjects\Models\AssignProject;
use App\Models\BaseModel;

class Team extends BaseModel implements IGeneric
{
    protected $table = 'generic_teams';

    protected $fillable = [
        'company_id',
		'name',
		'param_1',
        'param_2'
    ];

    	
	public function assignEmployees()
    {
        return $this->hasMany(AssignEmployee::class);
    }

    public function assignProjects()
    {
        return $this->hasMany(AssignProject::class);
    }
}
