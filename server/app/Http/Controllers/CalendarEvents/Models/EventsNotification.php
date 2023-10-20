<?php
namespace App\Http\Controllers\CalendarEvents\Models;

use App\Models\BaseModel;

class EventsNotification extends BaseModel
{
	protected $table = 'events_notifications';
	public static $snakeAttributes = false;

	protected $fillable = [
            'event_id',
            'user_id',
	];
}