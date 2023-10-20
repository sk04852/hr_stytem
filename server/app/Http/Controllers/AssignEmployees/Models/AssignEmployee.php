<?php

namespace App\Http\Controllers\AssignEmployees\Models;

use App\Http\Controllers\EmsEmployee\Models\Employee;
use App\Http\Controllers\Generics\Models\Team;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Models\BaseModel;

/**
 * Class AssignEmployee
 * 
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|GeneralSetting[] $generalSettings
 *
 * @package App\Models
 */
class AssignEmployee extends BaseModel
{
	protected $table = 'assign_employees';
	public static $snakeAttributes = false;

	protected $fillable = [
		'employee_id',
		'team_id',
		'status'
	];

	public function employees() {
		return $this->belongsTo(Employee::class, 'employee_id');
	}

	public function teams() {
		return $this->belongsTo(Team::class, 'team_id');
	}


}
