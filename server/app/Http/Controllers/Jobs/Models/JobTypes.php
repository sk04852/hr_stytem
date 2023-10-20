<?php

namespace App\Http\Controllers\Jobs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobTypes extends Model
{
    use SoftDeletes;

    protected $table = 'job_types';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'type',
    ];


    /**
     * Get the CandidateCV record associated with the Job History.
     */
    public function candidatecv()
    {
        return $this->belongsToMany('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'candidate_job_types', 'job_type_id', 'candidatecv_id');
    }
}
