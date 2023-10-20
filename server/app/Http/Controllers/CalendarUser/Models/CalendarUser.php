<?php

/**
 * Created by Reliese Model.
 */

namespace App\Http\Controllers\CalendarUser\Models;

use App\Http\Controllers\Calendar\Models\Calendar;
use App\Http\Controllers\Users\Models\User;
use Carbon\Carbon;
use App\Models\BaseModel;


/**
 * Class Calendar
 * 
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @package App\Models
 */
class CalendarUser extends BaseModel
{
    protected $table = 'calendar_users';
	public static $snakeAttributes = false;

	protected $fillable = [
        'calendar_id',
        'user_id',
        'invited_by'
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    
    public function calendars()
    {
        return $this->belongsTo(Calendar::class);
    }
    
}
