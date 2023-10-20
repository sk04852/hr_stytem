<?php

namespace App\Http\Controllers\Languages\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $table = 'languages';

    protected $fillable = [
        'name',
    ];

    /**
     * Get the Candidate CV record associated with the language.
     */
    public function candidateCv()
    {
        return $this->hasOne('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'mother_language_id', 'id');
    }

    /**
     * Get the Candidates record associated with the skill.
     */
    public function candidatesCv()
    {
        return $this->belongsToMany('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'candidatecv_language', 'language_id', 'candidatecv_id');
    }

    public function jobWorkinglanguages (){
        return $this->belongsToMany('App\Http\Controllers\Jobs\Models\JobPr', 'jobs_work_language', 'language_id', 'job_pr_id');
    }

    public function jobDesiredlanguages (){
        return $this->belongsToMany('App\Http\Controllers\Jobs\Models\JobPr', 'jobs_desired_language', 'language_id', 'job_pr_id');
    }

    public function LanguagesFilter($request){

        return Language::when(!empty($request->LanguageID), function ($query) use ($request) {
            return $query->where('Language-ID', '=', $request->LanguageID);
        })
        ->when(!empty($request->nameD), function ($query) use ($request) {
            $query->where('NAME', $request->nameD);
        });
    }

}
