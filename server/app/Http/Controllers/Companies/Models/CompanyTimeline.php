<?php

namespace App\Http\Controllers\Companies\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyTimeline extends Model
{
    protected $table = 'company_timeline';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'company_pr_id',
        'candidatecv_id',
        'user_pr_id',
        'action_name',
        'comments'
    ];

    protected $hidden = [
//        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function companyPr(){
        return $this->belongsTo('App\Http\Controllers\Companies\Models\CompanyPr', 'company_pr_id', 'id');
    }

    public function user(){
        return $this->belongsTo('App\Models\UserPr', 'user_pr_id', 'id');
    }

    public function candidate(){
        return $this->belongsTo('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'candidatecv_id', 'id');
    }

}
