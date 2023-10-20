<?php

namespace App\Http\Controllers\Jobs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobsFile extends Model
{
    use SoftDeletes;

    protected $table = 'jobs_files';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'job_pr_id',
        'file_name',
        'path'
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
        return asset('storage/'.$value);
    }

}
