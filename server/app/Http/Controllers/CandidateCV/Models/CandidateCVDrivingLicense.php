<?php

namespace App\Http\Controllers\CandidateCV\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateCVDrivingLicense extends Model
{
    use SoftDeletes;

    protected $table = 'candidatecv_driving_licenses';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'level',
        'issue_date',
        'expiry_date'
    ];

    /**
     * Get the CandidateCV record associated with the Candidate Education.
     */
    public function candidatecv()
    {
        return $this->belongsTo('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'candidatecv_id', 'id');
    }


}
