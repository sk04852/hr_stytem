<?php

/**
 * Created by Reliese Model.
 */

namespace App\Http\Controllers\Calendar\Models;

use App\Http\Controllers\CalendarEvents\Models\CalendarEvent;
use App\Http\Controllers\CalendarUser\Models\CalendarUser;
use Carbon\Carbon;
use App\Models\BaseModel;
use App\Http\Controllers\Users\Models\User;


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
class Calendar extends BaseModel
{
    protected $table = 'calendars';
	public static $snakeAttributes = false;

	protected $fillable = [
        'title',
        'visibility',
        'created_by',
        'source',
        'is_default',
        'google_calendar_id'
    ];


    public function scopeFilter($query, array $filters)
    {
        if (array_key_exists('title', $filters)) {
            $query->where('title', 'like', '%'.$filters['title'].'%');
        }
        if (array_key_exists('visibility', $filters)) {
            $query->where('visibility', '=', $filters['visibility']);
        }
        if (array_key_exists('created_by', $filters)) {
            $query->where('created_by', $filters['created_by']);
        }
    }

    public function user()
	{
		return $this->belongsTo(User::class , 'created_by');
    }
    public function events()
	{
        return $this->hasMany(CalendarEvent::class);
    }
    
    public function calendarUsers()
	{
		return $this->hasMany(CalendarUser::class);
	}


}
