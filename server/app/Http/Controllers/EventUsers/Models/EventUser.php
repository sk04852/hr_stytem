<?php

namespace App\Http\Controllers\EventUsers\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\BaseModel;
class EventUser extends BaseModel
{
	protected $table = 'calendar_event_users';
	public static $snakeAttributes = false;

	
	protected $fillable = [
		'event_id',
		'user_id',
    ];
}
