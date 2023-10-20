<?php

namespace App\Http\Controllers\Generics\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class Area extends BaseModel implements IGeneric
{
    use SoftDeletes;
    protected $table = 'generic_areas';
	protected $fillable = [
        'company_id',
		'name',
		'param_1',
        'param_2'
    ];
}
