<?php

namespace App\Http\Controllers\EducationDegrees\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EducationLevel extends Model
{
    use SoftDeletes;

    protected $table = 'education_levels';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the Candidate CV record associated with the language.
     */
    public function candidatecv_educations()
    {
        return $this->hasMany('App\Http\Controllers\CandidateCV\Models\CandidateCVEducation', 'level_id', 'id');
    }
}
