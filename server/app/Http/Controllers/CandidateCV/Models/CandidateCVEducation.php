<?php

namespace App\Http\Controllers\CandidateCV\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateCVEducation extends Model
{
    use SoftDeletes;

    protected $table = 'candidatecv_education';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'candidatecv_id',
        'degree_id',
        'level_id',
        // 'degree_name',
        'institute',
        // 'starting_date',
        // 'ending_date',
        'starting_year',
        'starting_month',
        'starting_day',
        'ending_year',
        'ending_month',
        'ending_day',
        'speciality',
        'still_studying',
        'additonal_information'

    ];

    /**
     * Get the CandidateCV record associated with the Candidate Education.
     */
    public function candidatecv()
    {
        return $this->belongsTo('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'candidatecv_id', 'id');
    }

    /**
     * Get the Degree record associated with the Candidate Education.
     */
    public function education_degree()
    {
        return $this->belongsTo('App\Http\Controllers\EducationDegrees\Models\EducationDegree', 'degree_id', 'id');
    }

    /**
     * Get the Degree record associated with the Candidate Education.
     */
    public function education_level()
    {
        return $this->belongsTo('App\Http\Controllers\EducationDegrees\Models\EducationLevel', 'level_id', 'id');
    }
}
