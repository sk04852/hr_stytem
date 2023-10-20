<?php

namespace App\Http\Controllers\EmployeesPolicies\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Models\BaseModel;

class EmployeePolicy extends BaseModel
{
	protected $table = 'employee_policies';
	public static $snakeAttributes = false;

	protected $fillable = [
		'employee_id',
		'policy_id'
	];

}
