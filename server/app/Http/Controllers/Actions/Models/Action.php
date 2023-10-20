<?php

namespace App\Http\Controllers\Actions\Models;

use App\Http\Controllers\CandidateCV\Models\CandidateCV;
use App\Http\Controllers\Jobs\Models\JobPr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Action extends Model
{
    const GENERAL_STATUS = 1;
    const APPLIED_STATUS = 2;
    const PLACED_STATUS = 7;
    const RENEWAL_STATUS = 8;

    use SoftDeletes;

    protected $table = 'actions';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'counts'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function jobs(){
        return $this->belongsToMany(JobPr::class, 'candidatecv_jobs', 'action_id', 'job_pr_id');
    }

    public function candidatecv(){
        return $this->belongsToMany(CandidateCV::class, 'candidatecv_jobs', 'action_id', 'candidatecv_id');
    }
}
