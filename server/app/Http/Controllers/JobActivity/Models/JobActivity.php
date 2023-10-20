<?php

namespace App\Http\Controllers\JobActivity\Models;

use Illuminate\Database\Eloquent\Model;

class JobActivity extends Model
{
    protected $table = 'job_activities';
    public static $snakeAttributes = false;
    public $timestamps = false;

    protected $fillable = [
        'job_id',
        'comment',
        "creator",
        "created_at",
        "activityId"
    ];


}
