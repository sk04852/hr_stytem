<?php

namespace App\Http\Controllers\CandidateCV\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateCVRecommender extends Model
{
    use SoftDeletes;

    protected $table = 'candidatecv_recommenders';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'recommendation'
    ];

    /**
     * Get the CandidateCV record associated with the Candidate Education.
     */
    public function candidatecv()
    {
        return $this->belongsTo('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'candidatecv_id', 'id');
    }


}
