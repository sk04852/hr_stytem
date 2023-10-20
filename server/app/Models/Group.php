<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;

/**
 * Class Group
 * 
 * @property int $id
 * @property string $group_name
 * @property string|null $is_default
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Group extends BaseModel
{
	protected $table = 'groups';
	public static $snakeAttributes = false;

	protected $fillable = [
		'group_name',
		'is_default'
	];
}
