<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;

/**
 * Class GroupUser
 * 
 * @property int $id
 * @property int $user_id
 * @property int $group_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class GroupUser extends BaseModel
{
	protected $table = 'group_user';
	public static $snakeAttributes = false;

	protected $casts = [
		'user_id' => 'int',
		'group_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'group_id'
	];
}
