<?php

namespace App\Http\Controllers\GoogleApi\Models;

use App\Http\Controllers\CalendarUser\Models\CalendarUser;
use App\Http\Controllers\GoogleApi\Models\GoogleCalenderEvent;
use App\Jobs\Google\SynchronizeGoogleEvents;
use App\Models\UserPr;
use Carbon\Carbon;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

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
class GoogleCalendar extends BaseModel
{
    use SoftDeletes;

    protected $table = 'google_calendars';

	public static $snakeAttributes = false;

	protected $fillable = [
        'user_pr_id',
        'title',
        'visibility',
        'created_by',
        'source',
        'is_default',
        'is_checked',
        'google_calendar_id'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($google_calendar) {
            SynchronizeGoogleEvents::dispatch($google_calendar);
        });
    }


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

    public function userPr()
	{
		return $this->belongsTo(UserPr::class , 'user_pr_id', 'id');
    }
    public function events()
	{
        return $this->hasMany(GoogleCalenderEvent::class, 'google_calendar_id', 'id');
    }

    public function calendarSharedWith (){
        return $this->belongsToMany(UserPr::class, 'google_calendar_users', 'google_calendar_id', 'user_pr_id')->withPivot('invited_by_id', 'rule_id', 'role')->withTimestamps();
    }

//    public function calendarUsers()
//	{
//		return $this->hasMany(CalendarUser::class);
//	}


}
