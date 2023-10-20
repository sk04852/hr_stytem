<?php

namespace App\Http\Controllers\Jobs\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use SoftDeletes;

    protected $table = 'jobs';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'job_pr_id',
        'company_pr_id',
        'offer_name',
        'title',
        "benefits",
        "location",
        "department",
        'description',
        "requirements",
        "comments",
        "additional_information",
        'recess'
    ];


    public function jobPr (){
        return $this->belongsTo('App\Http\Controllers\Jobs\Models\JobPr', 'job_id', 'id');
    }
}
