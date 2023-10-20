<?php

namespace App\Http\Controllers\CandidateCV\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateCVAdditionalCourse extends Model
{
    use SoftDeletes;

    protected $table = 'candidatecv_additional_courses';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'candidatecv_id',
        'title',
        'description',
        // 'start_date',
        // 'end_date',
        'starting_year',
        'starting_month',
        'starting_day',
        'ending_year',
        'ending_month',
        'ending_day',
        'total_hours'

    ];

    /**
     * Get the CandidateCV record associated with the Candidate Education.
     */
    public function candidatecv()
    {
        return $this->belongsTo('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'candidatecv_id', 'id');
    }
}
