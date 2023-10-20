<?php

namespace App\Http\Controllers\CandidateCV\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateCVFiles extends Model
{
    use SoftDeletes;

    protected $table = 'candidatecv_files';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'candidatecv_id',
        'file_name',
        'path',
        'user_pr_id'
    ];

    /**
     * Get the CandidateCV record associated with the Job History.
     */
    public function candidatecv()
    {
        return $this->belongsTo('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'candidatecv_id', 'id');
    }

    public function getPathAttribute($value)
    {
        return asset('storage/'.$value);
    }

    /**
     * Get User for the CandidateCv file.
     */
    public function userPr()
    {
        return $this->belongsTo('App\Models\UserPr', 'user_pr_id', 'id');
    }


}
