<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;

/**
 * Class AclRolePermission
 * 
 * @property int $id
 * @property string $role_name
 * @property int $role_id
 * @property string $permission_name
 * @property string $permission_slugn
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property AclRole $aclRole
 *
 * @package App\Models
 */
class AclRolePermission extends BaseModel
{
	protected $table = 'acl_role_permissions';
	public static $snakeAttributes = false;

	protected $casts = [
		'role_id' => 'int'
	];

	protected $fillable = [
		'role_name',
		'role_id',
		'permission_name',
		'permission_slugn'
	];

	public function aclRole()
	{
		return $this->belongsTo(AclRole::class, 'role_id');
	}
}
