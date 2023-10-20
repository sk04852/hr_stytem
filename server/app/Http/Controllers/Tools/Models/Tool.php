<?php

namespace App\Http\Controllers\Tools\Models;

use App\Http\Controllers\Companies\Models\Company;
use App\Models\BaseModel;

class Tool extends BaseModel
{
	protected $table = 'tools';

	protected $fillable = [

		'name',
		'url',
		'username',
		'password',
		'description',
		'company_id'
	];

	public function toolsFilters($request){
        
        return Tool::when(!empty($request->username), function ($query) use ($request) {
            return $query->where('username', '=', $request->username);
        })
		->when(!empty($request->name), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
		})
		->when(!empty($request->url), function ($query) use ($request) {
			$query->where('url',$request->url);
	    })
        ->when(!empty($request->from), function ($query) use ($request) {
            return  $query->whereDate('created_at', '>=', $request->from);
        })
        ->when(!empty($request->to), function ($query) use ($request) {
            return  $query->whereDate('created_at', '<=', $request->to);
        }); 
    }

	public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeWithCompany($query) {
        return $query->with(['company' => function($query){ $query->select(['id','company_name']);}]);
	}

}