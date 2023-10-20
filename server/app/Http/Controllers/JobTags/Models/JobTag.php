<?php

namespace App\Http\Controllers\JobTags\Models;

use Illuminate\Database\Eloquent\Model;

class JobTag extends Model
{
    protected $table = 'job-tag';
    public static $snakeAttributes = false;
    public $timestamps = false;

    protected $fillable = [
        'Tag-ID',
        'NAME',
    ];

    public function jobTagsFilter($request){

        return JobTag::when(!empty($request->TagID), function ($query) use ($request) {
            return $query->where('Tag-ID', '=', $request->TagID);
        })
        ->when(!empty($request->name), function ($query) use ($request) {
            $query->where('NAME', 'like', '%' . $request->name . '%');
        });
    }

}
