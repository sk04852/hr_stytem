<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserSmtpSetting
 * 
 * @property int $id
 * @property int $user_id
 * @property string $server_name
 * @property string $port
 * @property string $user_name
 * @property string $password
 * @property string|null $from_email
 * @property bool $requires_authentication
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property User $user
 *
 * @package App\Models
 */
class UserSmtpSetting extends BaseModel
{
	use SoftDeletes;
	protected $table = 'user_smtp_settings';
	public static $snakeAttributes = false;

	protected $casts = [
		'user_id' => 'int',
		'requires_authentication' => 'bool'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'user_id',
		'server_name',
		'port',
		'user_name',
		'password',
		'from_email',
		'requires_authentication'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
