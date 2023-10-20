<?php

namespace App\Http\Controllers\Topics\Models;

use App\Models\BaseModel;

class Topic extends BaseModel
{
    protected $table = 'topics';
    public static $snakeAttributes = false;

	protected $fillable = [
        'title',
        'subject_id'
	];


    public function topicsFilters($request){
        return Topic::when(!empty($request->title), function ($query) use ($request) {
            return $query->where('title', 'like', '%' . $request->title . '%');
        })
        ->when(!empty($request->subject_id), function ($query) use ($request) {
            return  $query->where('subject_id', $request->subject_id);
        })
        ->when(!empty($request->from), function ($query) use ($request) {
            return  $query->whereDate('created_at', '>=', $request->from);
        })
        ->when(!empty($request->to), function ($query) use ($request) {
            return  $query->whereDate('created_at', '<=', $request->to);
        });
    }

}
