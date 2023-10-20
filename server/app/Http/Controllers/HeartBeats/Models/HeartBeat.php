<?php

namespace App\Http\Controllers\HeartBeats\Models;

use App\Models\BaseModel;

class HeartBeat extends BaseModel
{
	protected $table = 'heart_beats';
	public static $snakeAttributes = false;

	protected $fillable = [
		"url",
		"method",
		"execute_after",
		"execute_after_error",
		"variables",
		"last_checked_at",
		"last_response",
		"notification_method",
		"notification_emails",
		"admin_notified",
	];

}
