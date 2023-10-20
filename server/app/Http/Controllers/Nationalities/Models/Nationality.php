<?php

namespace App\Http\Controllers\Nationalities\Models;

use Illuminate\Database\Eloquent\Model;

class Nationality extends Model
{
    protected $table = 'nationalities';

    protected $fillable = [
        'name',
    ];

    /**
     * Get the Candidate CVs record associated with the Nationality.
     */
    public function candidateCvs()
    {
        return $this->belongsToMany('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'candidatecv_nationality', 'nationality_id', 'candidatecv_id');
    }
}
