<?php

namespace App\Http\Controllers\Workflows\Models;

use App\Http\Controllers\Companies\Models\Company;
use App\Models\BaseModel;
use App\Models\Module;

class Workflow extends BaseModel
{
	protected $table = 'workflows';
	public static $snakeAttributes = false;

	protected $fillable = [
		'module_id',
		'company_id',
		'description',
		'execution'
	];
    public function filter($request){
		return $this->when(!empty($request->module_id), function ($query) use ($request) {
			return  $query->where('module_id', $request->module_id);
		})
		->when(!empty($request->description), function ($query) use ($request) {
			return  $query->where('description', $request->description);
		})
		->when(!empty($request->execution), function ($query) use ($request) {
			return $query->where('execution', $request->execution);
		});
	}

	public function module()
	{
		return $this->belongsTo(Module::class, 'module_id');
	}

	public function company()
	{
		return $this->belongsTo(Company::class, 'company_id');
	}
	
	public function scopeWithModule($query) {
        return $query->with(['module' => function($query){ $query->select(['id','name']);}]);
	}
}
