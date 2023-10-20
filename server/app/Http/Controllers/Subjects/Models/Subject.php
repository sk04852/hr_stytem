<?php

namespace App\Http\Controllers\Subjects\Models;

use App\Models\BaseModel;

class Subject extends BaseModel
{
    protected $table = 'subjects';
    public static $snakeAttributes = false;

	protected $fillable = [
        'title',
        'class_id'
	];


    public function subjectsFilters($request){
        return Subject::when(!empty($request->title), function ($query) use ($request) {
            return $query->where('title', 'like', '%' . $request->title . '%');
        })
        ->when(!empty($request->class_id), function ($query) use ($request) {
            return  $query->where('class_id', $request->class_id);
        })
        ->when(!empty($request->from), function ($query) use ($request) {
            return  $query->whereDate('created_at', '>=', $request->from);
        })
        ->when(!empty($request->to), function ($query) use ($request) {
            return  $query->whereDate('created_at', '<=', $request->to);
        });
    }

}
