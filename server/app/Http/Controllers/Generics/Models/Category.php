<?php

namespace App\Http\Controllers\Generics\Models;

use App\Http\Controllers\Companies\Models\Company;
use App\Models\BaseModel;

class Category extends BaseModel implements IGeneric
{
    protected $table = 'generic_categories';

    protected $fillable = [
        'name',
        'param_1',
        'param_2',
        'company_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeWithCompany($query) {
        return $query->with(['company' => function($query){ $query->select(['id','company_name']);}]);
	}

}
