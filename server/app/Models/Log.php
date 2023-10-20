<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;

/**
 * Class Log
 * 
 * @property int $id
 * @property int $user_id
 * @property string|null $action_type
 * @property string|null $module
 * @property string $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User $user
 *
 * @package App\Models
 */
class Log extends BaseModel
{
	protected $table = 'logs';
	public static $snakeAttributes = false;

	protected $casts = [
		'user_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'action_type',
		'module',
		'data'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
