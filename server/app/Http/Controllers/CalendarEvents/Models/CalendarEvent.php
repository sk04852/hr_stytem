<?php
namespace App\Http\Controllers\CalendarEvents\Models;

use Carbon\Carbon;
use App\Models\BaseModel;
use App\Events\WorkflowEvent;

use App\Http\Controllers\Calendar\Models\Calendar;
use App\Http\Controllers\Users\Models\User;
use App\Http\Controllers\EventUsers\Models\EventUser;

class CalendarEvent extends BaseModel
{
	protected $table = 'calendar_events';
	public static $snakeAttributes = false;

	protected $fillable = [
            "calendar_id",
            "subject",
            "type",
            "activity_type",
            "start_date_time",
            "end_date_time",
            "visibility",
            "notification",
            "status",
            "priority",
            "assigned_to",
            "created_by",
            "location",
            "g_event_id",
            "description",
            "created_at",
            "updated_at",
    ];
    
    //workflow
    protected static function booted()
    {
        static::saved(function($model){
            event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'saved'));
        });

        static::created(function($model){
            event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'create'));
        });

        static::updated(function($model){
            event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'update'));
        });
        
        static::deleted(function($model){
            event(new WorkflowEvent(getModuleIdFromEntity($model), $model, 'delete'));
        });
    }

    /* Relation  */
    public function calendar()
	{
		return $this->belongsTo(Calendar::class);
    }
   
    public function user()
	{
        return $this->belongsTo(User::class , 'assigned_to');
    }

    public function scopeMyEntries($query, $userId) {
        $eventsIds = EventUser::select('event_id')->where('user_id',$userId)->get();
        $query->with('user')
                ->where('created_by', $userId)
                ->orWhere('assigned_to', $userId)
                ->orWhere('visibility', 'Public')
                ->orWhereIn('id',$eventsIds);
    }

}