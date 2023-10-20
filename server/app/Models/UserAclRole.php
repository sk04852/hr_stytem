<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserAclRole
 * 
 * @property int $id
 * @property int $user_id
 * @property string $role
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property User $user
 *
 * @package App\Models
 */
class UserAclRole extends BaseModel
{
	use SoftDeletes;
	protected $table = 'user_acl_roles';
	public static $snakeAttributes = false;

	protected $casts = [
		'user_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'role'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
