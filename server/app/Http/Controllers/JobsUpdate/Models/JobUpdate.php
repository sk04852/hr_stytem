<?php

namespace App\Http\Controllers\JobsUpdate\Models;

use Illuminate\Database\Eloquent\Model;

class JobUpdate extends Model
{
    protected $table = 'updatejobs';
    
    public static $snakeAttributes = false;
    public $timestamps = false;

    protected $fillable = [
        'job_ID',
        'title',
        'status',
        'tag_ID',
        'type_of_job',
        'category_ID',
        'update_ID',
        'category',
        'location',
        'contact',
        'doc_ID',
        'doc',
        'jobDescription',
        'divDescription',
        'comments',
        'benefits',
        "created_at",
        "required",
        "creator",
        "company",
    ];

    // public function jobsFilter($request){

    //     return Job::when(!empty($request->JobID), function ($query) use ($request) {
    //         return $query->where('Job-ID', '=', $request->JobID);
    //     })
    //     ->when(!empty($request->status), function ($query) use ($request) {
    //         $query->where('Status', 'like', '%' . $request->status . '%');
    //     });
    // }

}
