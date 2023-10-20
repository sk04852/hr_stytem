<?php

namespace App\Http\Controllers\Skills\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skill extends Model
{
    use SoftDeletes;

    protected $table = 'skills';

    public static $snakeAttributes = false;

    public $timestamps = true;

    protected $fillable = [
        'name',
    ];

    /**
     * Get the Candidates record associated with the skill.
     */
    public function skills()
    {
        return $this->belongsToMany('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'candidatecv_skill', 'skill_id', 'candidatecv_id');
    }

    public function skillsFilter($request){

        return Skill::when(!empty($request->name), function ($query) use ($request) {
            $query->where('NAME', 'like', '%' . $request->name . '%');
        });
    }

}
