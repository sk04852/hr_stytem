<?php

namespace App\Http\Controllers\HeartBeats\Models;

use App\Models\BaseModel;

class HeartBeatLog extends BaseModel
{
	protected $table = 'heart_beat_logs';
	public static $snakeAttributes = false;

	protected $fillable = [
		"url_id",
		"error_code",
		"response"
	];

}
