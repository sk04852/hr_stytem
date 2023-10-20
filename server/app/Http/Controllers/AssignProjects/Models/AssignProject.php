<?php

namespace App\Http\Controllers\AssignProjects\Models;

use App\Http\Controllers\Generics\Models\Team;
use App\Http\Controllers\Project\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Models\BaseModel;

/**
 * Class AssignProjects
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
class AssignProject extends BaseModel
{
	protected $table = 'assign_projects';
	public static $snakeAttributes = false;

	protected $fillable = [
		'project_id',
		'team_id',
	];

	public function projects() {
		return $this->belongsTo(Project::class, 'project_id');
	}

	public function teams() {
		return $this->belongsTo(Team::class, 'team_id');
	}

}
