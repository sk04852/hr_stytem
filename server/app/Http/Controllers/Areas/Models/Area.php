<?php

namespace App\Http\Controllers\Areas\Models;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class Area extends BaseModel
{
	use SoftDeletes;

	protected $table = 'areas';

	protected $fillable = [

		'name',
		'params1',
		'params2',
		
    ];
	
}
