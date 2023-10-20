<?php

namespace App\Http\Controllers\JobProfiles\Models;

use Illuminate\Database\Eloquent\Model;

class JobProfile extends Model
{
    protected $table = 'job-pr';
    public static $snakeAttributes = false;
    public $timestamps = false;

    protected $fillable = [
        'jobpr-ID',
        'Job-ID',
        'workers',
        'companypr-ID',
        'userpr-ID',
        'Deadline',
        'Site',
        'salary',
        'Bonous',
        'Optional',
        'Date-created',
    ];

    public function JobProfilesFilter($request){

        return JobProfile::when(!empty($request->JobID), function ($query) use ($request) {
            return $query->where('Job-ID', $request->JobID);
        })
        ->when(!empty($request->JobPrID), function ($query) use ($request) {
            $query->where('jobpr-ID', $request->JobPrID);
        });
    }

}
