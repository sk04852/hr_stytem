<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserAclPermission
 * 
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property User $user
 *
 * @package App\Models
 */
class UserAclPermission extends BaseModel
{
	use SoftDeletes;
	protected $table = 'user_acl_permissions';
	public static $snakeAttributes = false;

	protected $casts = [
		'user_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'name',
		'slug'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
