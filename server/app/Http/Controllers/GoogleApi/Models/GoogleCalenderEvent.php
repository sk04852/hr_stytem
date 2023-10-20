<?php

namespace App\Http\Controllers\GoogleApi\Models;

use App\Http\Controllers\GoogleApi\Models\GoogleCalendar;
use App\Models\UserPr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoogleCalenderEvent extends Model
{
    use SoftDeletes;

    protected $table = 'google_calendar_events';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'google_calendar_id',
        'user_pr_id',
        'subject',
        'type',
        'activity_type',
        'visibility',
        'notification',
        'priority',
        'assigned_to',
        'location',
        'start',
        'end',
        'status',
        'is_all_day',
        'title',
        'description',
        'transparency',
        'google_event_id'
    ];

    public function userPr(){
        return $this->belongsTo(UserPr::class, 'user_pr_id', 'id');
    }

    public function assignedTo(){
        return $this->belongsTo(UserPr::class, 'assigned_to', 'id');
    }

    public function calendar(){
        return $this->belongsTo(GoogleCalendar::class, 'google_calendar_id');
    }

    public function getStartAttribute($start)
    {
        return $this->asDateTime($start)->setTimezone($this->calendar->timezone);
    }

    public function getEndAttribute($end)
    {
        return $this->asDateTime($end)->setTimezone($this->calendar->timezone);
    }

    public function getDurationAttribute()
    {
        return $this->start->diffForHumans($this->ended, true);
    }
}
