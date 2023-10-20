<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AclRole
 * 
 * @property int $id
 * @property bool $is_assigned_to_all
 * @property string $name
 * @property string|null $slug
 * @property string|null $description
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|AclRolePermission[] $aclRolePermissions
 *
 * @package App\Models
 */
class AclRole extends BaseModel
{
	use SoftDeletes;
	protected $table = 'acl_roles';
	public static $snakeAttributes = false;

	protected $casts = [
		'is_assigned_to_all' => 'bool'
	];

	protected $fillable = [
		'is_assigned_to_all',
		'name',
		'slug',
		'description',
		'status'
	];

	public function aclRolePermissions()
	{
		return $this->hasMany(AclRolePermission::class, 'role_id');
	}
}
