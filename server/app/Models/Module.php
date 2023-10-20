<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Http\Controllers\Acl\Models\Permission;
use App\Http\Controllers\Workflows\Models\Workflow;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class Module
 *
 * @property int $id
 * @property string $name
 * @property string $namespace
 * @property string $status
 *
 * @property Collection|MediaDriveRelation[] $mediaDriveRelations
 * @property Collection|SmtpEntityRelationship[] $smtpEntityRelationships
 *
 * @package App\Models
 */
class Module extends BaseModel
{
	protected $table = 'modules';
	public $timestamps = false;
	public static $snakeAttributes = false;

	protected $fillable = [
		'name',
		'namespace',
        'default_permissions',
		'status'
	];

	public function mediaDriveRelations()
	{
		return $this->hasMany(MediaDriveRelation::class);
	}

	public function smtpEntityRelationships()
	{
		return $this->hasMany(SmtpEntityRelationship::class);
    }

    public function toDigest() {
        return $this->select(['id', 'name', 'status'])->get();
	}

	public function workflows()
	{
		return $this->hasMany(Workflow::class);
    }

    public function permissions()
	{
		return $this->hasMany(Permission::class)->select(['id', 'name', 'type', 'description']);
    }

}
