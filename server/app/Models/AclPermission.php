<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AclPermission
 * 
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property string|null $description
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class AclPermission extends BaseModel
{
	use SoftDeletes;
	protected $table = 'acl_permissions';
	public static $snakeAttributes = false;

	protected $fillable = [
		'name',
		'slug',
		'description'
	];
}
