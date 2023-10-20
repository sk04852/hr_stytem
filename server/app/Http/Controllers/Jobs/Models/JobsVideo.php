<?php

namespace App\Http\Controllers\Jobs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobsVideo extends Model
{
    use SoftDeletes;

    const VIDEO_TYPE_FILE = 1;
    const VIDEO_TYPE_LINK = 2;

    protected $table = 'jobs_videos';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'job_pr_id',
        'type',
        'file_name',
        'path',
        'link'
    ];

    /**
     * Get the CandidateCV record associated with the Job History.
     */
    public function jobPr()
    {
        return $this->belongsTo('App\Http\Controllers\Jobs\Models\JobPr', 'job_pr_id', 'id');
    }

    public function getPathAttribute($value)
    {
        if(!($this->type == 2))
            return asset('storage/'.$value);
        return $value ;
    }

}
