<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Altek\Accountant\Contracts\Identifiable;

class UserPr extends User implements Identifiable
{
    use HasApiTokens, Notifiable, HasFactory, HasRoles, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_pr';

    protected $fillable = [
        'uuid',
        'userpr_ID',
        'user_ID',
        'photo',
        'int_code',
        'phone',
        'email',
        'password',
        'timezone_id',
        'permission_ID',
        'zoom_id',
        'zoom_personal_link',
        'skype_id',
        'skype_personal_link',
        'google_calendar_access_token',
        'google_calendar_refresh_token',
        'google_calendar_user_account_info'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'google_calendar_access_token',
        'google_calendar_refresh_token',
        'google_calendar_user_account_info',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return $this->getKey();
    }

    public function getPhotoAttribute($value)
    {
        return asset('storage/'.$value);
    }

    public function timezone (){
        return $this->belongsTo('App\Http\Controllers\Timezones\Models\Timezone', 'timezone_id', 'id');
    }

    public function mailWildcards (){
        return $this->hasMany('App\Http\Controllers\Mail\Models\Wildcard', 'created_by', 'id');
    }

    public function mailTemplates (){
        return $this->hasMany('App\Http\Controllers\Mail\Models\Template', 'created_by', 'id');
    }

    public function hrTasks() {
        return $this->belongsToMany('App\Http\Controllers\HrTasks\Models\HrTask', 'hr_assigned_tasks', 'user_pr_id');
    }

    public function user () {
        return $this->hasOne('App\Http\Controllers\Users\Models\User', 'id', 'user_ID');
    }

    public function candidateRecruits(){
        return $this->belongsToMany('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'candidatecv_jobs', 'user_pr_id', 'candidatecv_id')->withPivot('job_pr_id', 'action_id')->withTimestamps();
    }

    public function candidateCVFiles(){
        return $this->hasMany('App\Http\Controllers\CandidateCV\Models\CandidateCVFiles', 'user_pr_id', 'id');
    }

    public function googleCalendars(){
        return $this->hasMany('App\Http\Controllers\GoogleApi\Models\GoogleCalendar', 'user_pr_id', 'id');
    }

    public function sharedCalendars(){
        return $this->belongsToMany('App\Http\Controllers\GoogleApi\Models\GoogleCalendar', 'google_calendar_users', 'user_pr_id', 'google_calendar_id')->withPivot('invited_by_id', 'rule_id', 'role')->withTimestamps();
    }

    public function googleCalendarEvents(){
        return $this->hasMany('App\Http\Controllers\GoogleApi\Models\GoogleCalenderEvent', 'user_pr_id', 'id');
    }

    public function agrelloResponses(){
        return $this->hasMany('App\Http\Controllers\Agrello\Models\AgrelloResponse', 'user_pr_id', 'id');
    }
}
