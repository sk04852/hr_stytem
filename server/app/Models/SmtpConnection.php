<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SmtpConnection
 * 
 * @property int $id
 * @property string $title
 * @property string $server_name
 * @property string $port
 * @property string $user_name
 * @property string $password
 * @property string|null $from_email
 * @property bool $requires_authentication
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|SmtpEntityRelationship[] $smtpEntityRelationships
 *
 * @package App\Models
 */
class SmtpConnection extends BaseModel
{
	use SoftDeletes;
	protected $table = 'smtp_connections';
	public static $snakeAttributes = false;

	protected $casts = [
		'requires_authentication' => 'bool'
	];

	protected $hidden = [
		'password'
	];

	protected $fillable = [
		'title',
		'server_name',
		'port',
		'user_name',
		'password',
		'from_email',
		'requires_authentication',
		'status'
	];

	public function smtpEntityRelationships()
	{
		return $this->hasMany(SmtpEntityRelationship::class);
	}
}
