<?php

namespace App\Http\Controllers\Classes\Models;

use App\Models\BaseModel;

class Classs extends BaseModel
{
    protected $table = 'classes';
    public static $snakeAttributes = false;

	protected $fillable = [
        'name',
	];

    public function classesFilters($request){
        return Classs::when(!empty($request->name), function ($query) use ($request) {
            return $query->where('name', 'like', '%' . $request->name . '%');
        })
        ->when(!empty($request->from), function ($query) use ($request) {
            return  $query->whereDate('created_at', '>=', $request->from);
        })
        ->when(!empty($request->to), function ($query) use ($request) {
            return  $query->whereDate('created_at', '<=', $request->to);
        });
    }

}
