<?php

namespace App\Http\Controllers\Tags\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tags';

    protected $fillable = [
        'name',
    ];

    /**
     * Get the Candidate CVs record associated with the language.
     */
    public function candidateCvs()
    {
        return $this->belongsToMany('App\Http\Controllers\CandidateCV\Models\CandidateCV', 'candidatecv_tag', 'tag_id', 'candidatecv_id');
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
