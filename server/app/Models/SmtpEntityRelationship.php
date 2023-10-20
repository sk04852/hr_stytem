<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;

/**
 * Class SmtpEntityRelationship
 * 
 * @property int $id
 * @property int $module_id
 * @property int $relation_id
 * @property int $smtp_connection_id
 * @property string $default
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Module $module
 * @property SmtpConnection $smtpConnection
 *
 * @package App\Models
 */
class SmtpEntityRelationship extends BaseModel
{
	protected $table = 'smtp_entity_relationships';
	public static $snakeAttributes = false;

	protected $casts = [
		'module_id' => 'int',
		'relation_id' => 'int',
		'smtp_connection_id' => 'int'
	];

	protected $fillable = [
		'module_id',
		'relation_id',
		'smtp_connection_id',
		'default'
	];

	public function module()
	{
		return $this->belongsTo(Module::class);
	}

	public function smtpConnection()
	{
		return $this->belongsTo(SmtpConnection::class);
	}
}
