<?php

namespace App\Http\Controllers\CandidateCV\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateCVJobHistory extends Model
{
    use SoftDeletes;

    protected $table = 'candidatecv_job_history';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'candidatecv_id',
        'company_name',
        'designation',
        // 'starting_date',
        // 'ending_date',
        'starting_year',
        'starting_month',
        'starting_day',
        'ending_year',
        'ending_month',
        'ending_day',
        'still_working',
        'description',
        'work_place'
    ];

    /**
     * Get the CandidateCV record associated with the Job History.
     */
    public function candidatecv()
    {
        return $this->belongsTo('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'candidatecv_id', 'id');
    }


}
