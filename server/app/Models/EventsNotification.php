<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;

/**
 * Class EventsNotification
 * 
 * @property int $id
 * @property int $event_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class EventsNotification extends BaseModel
{
	protected $table = 'events_notifications';
	public static $snakeAttributes = false;

	protected $casts = [
		'event_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'event_id',
		'user_id'
	];
}
