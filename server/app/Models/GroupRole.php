<?php
namespace App\Models;

class GroupRole extends BaseModel
{
	protected $table = 'group_roles';
	public static $snakeAttributes = false;

	protected $fillable = [
		'role_id',
		'group_id'
	];
}
